<?php

namespace App\Http\Controllers;

use App\Services\CommissionService;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    protected $commissionService;

    public function __construct(CommissionService $commissionService) {
        $this->commissionService = $commissionService;
    }
    public function createUpdate (Request $request) {
        $commissions = $this->commissionService->createUpdate($request);
        return response()->json($commissions);
    }
}
