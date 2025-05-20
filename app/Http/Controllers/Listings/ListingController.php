<?php

namespace App\Http\Controllers\Listings;

use App\Exports\ListingsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ListingController\EditListingRequest;
use App\Http\Requests\ListingController\ParamsListing;
use App\Http\Requests\ListingController\StoreAcquisitionRequest;
use App\Http\Requests\ListingController\UpdateListingDraftRequest;
use App\Http\Requests\ListingController\UpdateListingRequest;
use App\Models\Agent;
use App\Models\Listing;
use App\Services\Listings\AcquisitionService;
use App\Services\Listings\ListingService;
use Illuminate\Support\Facades\Log;
use App\Services\IconnectService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Traits\ApiResponseTrait;
use App\Traits\AutenticationTrait;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ListingController extends Controller
{
    use ApiResponseTrait, AutenticationTrait;

    public function __construct(
        private ListingService $listingService,
        private AcquisitionService $acquisitionService,
        private IconnectService $iconnectService,
        private ImageService $imageService,
    ) {}

    public function index(ParamsListing $request)
    {
        $data = $this->listingService->getIndexData($request);
        return Inertia::render('Listings/Index', $data);
    }

    public function store(StoreAcquisitionRequest $request)
    {
        $listing = $this->acquisitionService->storeAcquisition($request->dto);
        return to_route('listings.edit', ['key' => $listing->key]);
    }

    public function edit(string $key, EditListingRequest $request)
    {
        $listingData = $this->listingService->getEditData(
            $key,
            $request->locationDto,
            $request->listingEditParamDto
        );

        return Inertia::render(
            'Listings/Edit',
            $listingData,
        );
    }

    public function update(string $key, UpdateListingRequest $request)
    {
        $dto = $request->dto;
        $listing = $this->listingService->updateListing($key, $dto);
        return to_route('listings.edit', ['key' => $listing->key]);
    }

    public function updateDraft(string $key, UpdateListingRequest $request)
    {
        $listing = $this->listingService->updateListing($key, $request->dto, true);
        return to_route('listings.edit', ['key' => $listing->key]);
    }

    public function updateTest(string $key, Request $request)
    {
        dd("Otro");
        // dd($request->all());
    }

    public function updateBuyers(Request $request)
    {
        return $this->listingService->updateBuyers($request);
    }

    public function storeExternal(StoreAcquisitionRequest $request)
    {

        $offi = Agent::where('id', $request->agent_id)->with(['office'])->first();
        $request->merge([
            'office_id' => $offi->office->id
        ]);

        $listing = $this->acquisitionService->storeAcquisitionExternal($request->all());
        return $this->successResponse($listing);
    }

    public function copy(string $key, Request $request)
    {
        $listing = $this->listingService->cloneListing($key, $request->all());
        return to_route('listings.edit', ['key' => $listing->key]);
    }

    public function downloadPdf(string $key)
    {
        return $this->listingService->downloadPdf($key);
    }

    public function downloadExcel(Request $request)
    {
        $user = $this->getAuthenticate();
        $query = $request->query();

        // dd($query);

        if ($user?->roles[0]->name === 'Broker') {
            $query['office_id'] = $user?->agent?->office_id;
        }

        $filename = 'listings_' . now()->format('Ymd_His') . '.csv';

        // if (count($request->query()) > 0) {
        //     $path = storage_path('app/temp/' . $filename);

        //     if (!file_exists(dirname($path))) {
        //         mkdir(dirname($path), 0777, true);
        //     }

        //     $spreadsheet = $this->listingService->generateExcel($query);

        //     $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        //     $writer->save($path);

        //     return response()->download($path, $filename, [
        //         'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        //     ])->deleteFileAfterSend(true);
        // } else {
        // Exportar como CSV
        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            app(ListingService::class)->streamCsv($query, $handle);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
        // }
    }

    public function downloadCSV(Request $request)
    {
        $user = $this->getAuthenticate();
        $query = $request->query();

        if ($user?->roles[0]->name === 'Broker') {
            $query['office_id'] = $user?->agent?->office_id;
        }

        $filename = 'listings_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            app(ListingService::class)->streamCsv($query, $handle);
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function web(string $key)
    {
        $baseUrl = env('PUBLIC_URL');
        $url = "$baseUrl/detail/$key";

        return redirect()->away($url);
    }
}
