<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\ContractType;
use App\Models\Language;
use App\Models\ListingTransactionType;
use App\Models\SubtypeProperty;
use App\Services\Agents\OfficeService;
use App\Services\LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OptionController extends Controller
{
	protected $transactionService;
	protected $locationService;
	protected $officeService;
	protected $agentService;
	protected $listingService;
	protected $commissionService;

	public function __construct(
		LocationService $locationService,
		OfficeService $officeService
	) {

		$this->locationService = $locationService;
		$this->officeService = $officeService;
	}

	public function getDepartamentos(Request $request)
	{
		return $this->locationService->getDepartamentos($request);
	}

	public function getProvincias(Request $request)
	{
		return $this->locationService->getProvincias($request);
	}

	public function getCiudades(Request $request)
	{
		return $this->locationService->getCiudades($request);
	}

	public function getZonas(Request $request)
	{
		return $this->locationService->getZonas($request);
	}

	public function getListingTransactionType()
	{
		return  ListingTransactionType::get();
	}

	public function getSubType()
	{
		return  SubtypeProperty::get();
	}

	public function getContract()
	{
		return  ContractType::get();
	}
	public function getSelectOptions(Request $request)
	{
		$lang = DB::table('options as opt')
			->join('option_translations as optr', 'opt.id', 'optr.option_id')
			->join('languages as lang', 'lang.id', 'optr.language_id')
			->where('opt.category', '=', $request->category)
			->where('lang.code', '=', $request->code)
			->select('optr.key_name', 'optr.value')
			->orderBy('optr.value')->get();
		return response()->json($lang, 200);
	}
	public function getLang()
	{
		$lang = Language::select('code', 'name')->get();
		return response()->json($lang, 200);
	}

	public function getAgents()
	{
		$lang = DB::table('agents')
			->selectRaw('agents.id,
		users.name_to_show as agent
	')->join('users', 'users.id', '=', 'agents.user_id')
			->get();
		return response()->json($lang, 200);
	}


	public function getCategories(Request $request)
	{
		$lang = DB::table('category_contacts')
			->selectRaw('*')
			->get();
		return response()->json($lang, 200);

	}
}
