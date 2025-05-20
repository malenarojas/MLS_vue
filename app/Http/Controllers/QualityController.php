<?php

namespace App\Http\Controllers;

use App\Constants\ListingStatusId;
use App\Models\Listing;
use App\Models\ListingQualityControl;
use App\Models\StatusListing;
use App\Models\User;
use App\Services\Agents\AgentService;
use App\Services\Agents\OfficeService;
use App\Services\Listings\ListingService;
use App\Traits\AutenticationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QualityController extends Controller
{
    use AutenticationTrait;

    public function __construct(
        private OfficeService $officeService,
        private AgentService $agentService,
    ) {}

    public function index(Request $request)
    {
        $office_id = $request->office_id ?? null;
        $agent_id = $request->agent_id ?? null;
        $status_id = $request->status_id ?? null;
        $search = $request->search ?? null;

        $user = $this->getAuthenticate();
        $offices = [];
        if ($user->hasDirectPermission('listing.show_offices')) {
            $offices = $this->officeService->getAll();
        } else {
            $office_id = $user->agent?->office_id;
        }

        $agents = [];
        if ($user->hasDirectPermission('listing.show_agents')) {
            if ($office_id) {
                $agents = $this->agentService->filterAgents(compact('office_id'));
            }
        } else {
            if (!isset($agent_id)) {
                $agent_id = $user->agent?->id;
            }
        }

        // Correctly apply the filters using whereHas for relationships
        $listings = Listing::select(
            'listings.id',
            'listings.key',
            'listings.MLSID',
            'listings.created_at',
        )->with([
            'agents' => function ($query) {
                $query->select('agents.id', 'agents.user_id', 'agents.office_id')->with([
                    'user' => function ($query) {
                        $query->select('id', 'name_to_show');
                    },
                ])->limit(1);
            }
        ])
            ->when($office_id, function ($query) use ($office_id) {
                $query->whereHas('agents', function ($subQuery) use ($office_id) {
                    $subQuery->where('agents.office_id', $office_id);
                });
            })
            ->when($agent_id, function ($query) use ($agent_id) {
                $query->whereHas('agents', function ($subQuery) use ($agent_id) {
                    $subQuery->where('agents.id', $agent_id);
                });
            })
            ->when($status_id && $status_id !== 'all', function ($query) use ($status_id) {
                $query->where('status_listing_id', $status_id);
            })
            ->when($status_id === null || $status_id === 'all', function ($query) use ($status_id) {
                $query->whereIn('status_listing_id', [
                    ListingService::LISTING_STATUS_ACTIVE,
                    ListingService::LISTING_STATUS_REVIEW,
                    ListingService::LISTING_STATUS_REJECT,
                ]);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('listings.MLSID', 'like', "%$search%");
            })
            // ->whereIn('listings.status_listing_id', [ListingService::LISTING_STATUS_REVIEW, )
            ->paginate(20);

        return Inertia::render('QualityControl/Index', [
            'filters' => $request->all(),
            'listings' => $listings,
            'offices' => $offices,
            'agents' => $agents,
            'status' => StatusListing::select('id', 'name')->whereIn('id', [2, 9, 10])->get(),
        ]);
    }

    public function store(Request $request)
    {
        $MLSID = null;
        try {
            DB::beginTransaction();
            $listing = Listing::select('id', 'key', 'MLSID')
                ->where('key', $request->key)
                ->first();
            $oldValue = $listing->status_listing_id;
            // dd($listing);

            $user = $this->getAuthenticate();
            $userId = $user->id;
            $isApprove = $request->is_approve;

            ListingQualityControl::create([
                'comment' => $request->comment,
                'listing_id' => $listing->id,
                'user_id' => $userId,
                'status' => $isApprove,
            ]);

            $notes = '';
            if ($isApprove) {
                // Aprobado enviar a activa
                $listing->status_listing_id = ListingService::LISTING_STATUS_ACTIVE;
                $notes = 'Estado cambiado a Activa';
            } else {
                // Rechazado enviar a rechazado
                $listing->status_listing_id = ListingService::LISTING_STATUS_REJECT;
                $notes = 'Estado cambiado a Rechazado';
            }
            $listing->save();

            $listing->logs()->create([
                'listing_id' => $listing->id,
                'user_id' => $userId,
                'field_name' => 'status_listing_id',
                'old_value' => $oldValue,
                'new_value' => $listing->status_listing_id,
                'notes' => $notes,
            ]);
            DB::commit();

            return to_route('qualitycontrol.index', $listing->key);
        } catch (\Exception $e) {
            DB::rollBack();
            $msg = $MLSID ? " MLSID: $MLSID" : "";
            return back()->withErrors([
                'error' => "Error al aprobar o rechazar Listing $msg",
            ]);
        }
    }
}
