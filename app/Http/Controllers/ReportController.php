<?php

namespace App\Http\Controllers;

use App\Models\StatusListing;
use App\Services\Agents\OfficeService;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __construct(private OfficeService $officeService) {}

    public function index()
    {
        return Inertia::render('Reports/index');
    }

    public function transactionPayment()
    {
        return Inertia::render('Reports/transaction-payments');
    }

    public function listings()
    {
        $offices = $this->officeService->getOfficesWithFilters([
            'active_office' => true,
            'is_external' => false,
        ]);
        $listings_status = StatusListing::select('id', 'name')->get();

        return Inertia::render(
            'Reports/Listings',
            compact('offices', 'listings_status')
        );
    }
}
