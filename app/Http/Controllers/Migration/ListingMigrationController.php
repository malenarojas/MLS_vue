<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Models\StatusListing;
use App\Services\Migrations\ListingMigrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ListingMigrationController extends Controller
{
    public function __construct(private ListingMigrationService $migrationService) {}

    public function index()
    {
        $status = StatusListing::all();
        return Inertia::render('Migrations/Listings/Index', compact('status'));
    }

    public function indexInsert()
    {
        return Inertia::render('Migrations/Listings/IndexInsert');
    }

    public function storeInsert(Request $request)
    {
        try {
            Log::info("Iniciando insercion de Listings");
            $request->validate(['file' => 'required|mimes:xlsx,xls']);
            $file = $request->file('file');
            $tempPath = $file->getPathname();
            $this->migrationService->insertFromExcel($tempPath);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => "Error al migrar listings",
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'files.*' => 'nullable|mimes:xlsx,xls,json'
            ]);

            // dd($request->all());

            $files = $request->file('files');
            $activar = $request->activar ?? false;
            $status = $request->status ?? [];
            $migrateFromFirst = $request->migrateFromFirst ?? false; // Migra desde el primer registro
            $migrateAll = $request->migrateAll ?? false; // Ignorar el estado de la migracion

            if (!$files) {
                Log::info("Migrando desde la base de datos");
                // ListingMigrationFromApi::dispatch(
                //     MigrationConfig::from([
                //         'fromDB' => true,
                //         'migrateAll' => $migrateAll,
                //         'migrateFromFirst' => $migrateFromFirst,
                //     ])
                // );
                $this->migrationService->migrateFromDB($status, $migrateFromFirst);
                return back()->with('success', 'Migración en proceso.');
            }

            $i = 0;
            foreach ($files as $file) {
                $originalName = $file->getClientOriginalName();
                $tempPath = $file->getPathname();

                Log::info("$i: Archivo a migrar: $originalName");
                $i++;

                if ($file->getClientOriginalExtension() === 'json') {
                    Log::info("Migrando desde archivo json");
                    $this->migrationService->migrateFromJson($tempPath, $activar);
                } else if ($file->getClientOriginalExtension() === 'xlsx' || $file->getClientOriginalExtension() === 'xls') {
                    Log::info("Migrando desde archivo excel");
                    $this->migrationService->migrateFromExcel($tempPath, $activar, $status);
                }
            }

            return back()->with('success', 'Migración en proceso.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => "Error al migrar listings",
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function migrateFrom(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,xls,json',
                'status' => 'required|integer',
            ]);

            $file = $request->file('file');
            $tempPath = $file->getPathname();
            $this->migrationService->changedStatus($tempPath, (int) $request->status);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => "Error al migrar listings",
                'exception' => $e->getMessage(),
            ]);
        }
    }

    public function indexReport()
    {
        return Inertia::render('Migrations/Listings/IndexReport');
    }

    public function migrateFromReport(Request $request)
    {
        try {
            $this->migrationService->migrateReport();
            return back()->with('success', 'Migración completada correctamente.');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => "Error al migrar listings",
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
