<?php
namespace App\Services;

use App\Models\Agent;
use App\Models\Goal;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExecutiveResumeService 
{
    protected $menuService;

    public function __construct(MenuService $menuService) {
        $this->menuService = $menuService;
    }

    public function getExecutiveResumeDetails($data) 
    {
        // $query = Office::selectRaw('
        //     offices.name as office_name,
        //     offices.id as office_id,
        //     SUM(trasactions.current_liting_price) as transaction_volume,
        //     COUNT(transactions.id) as transactions,
        //     SUM(payments.amount) as payment_amount,
        //     COUNT(listings.id) as active_listings
        // ')
        // ->join('transactions', 'transactions.office_id', '=', 'offices.id')
        // ->join('agents', 'agents.office_id', '=', 'offices.office_id')
        // ->leftJoin('payments', 'payments.agent_internal_id', '=', 'agents.agent_internal_id')
        // ->leftJoin('agent_listing', 'agent_listing.agent_id', '=', 'agents.id')
        // ->leftJoin('listings', function ($query) use ($data) {
        //     $query->on('listings.id', '=', 'agent_listing.listing_id')
        //         ->whereRaw('
        //             LEAST(
        //                 IFNULL(listings.cancelation_date, "9999-01-01"),
        //                 IFNULL(listings.contract_end_date, "9999-01-01"),
        //                 IFNULL(transactions.sold_date, "9999-01-01")
        //             ) > ?
        //         ', $data['end_date'])
        //         ->whereRaw('
        //             listings.date_of_listing <= ?
        //         ', $data['end_date']);
        // })
        // ->where();

        // if(isset($data['office_id']) && $data['office_id'] != '') {
        //     $query->where('offices.id', $data['office_id']);
        // }
        // if(isset($data['agent_id']) && $data['agent_id'] != '') {
        //     $query->where('agents.id', $data['agent_id']);
        // }

        $higherMonth = max($data['months']);
        $today = Carbon::now()->format('Y-m-d');

        $data['last_date'] = Carbon::createFromFormat('Y-m-d', $data['year'].'-'.$higherMonth.'-01')->endOfMonth() > $today ?
            $today : Carbon::createFromFormat('Y-m-d', $data['year'].'-'.$higherMonth.'-01')->endOfMonth()->format('Y-m-d');

        $currentDate = Carbon::parse($data['last_date']);
        $previousDate = $currentDate->copy()->endOfMonth()->subYear();
            
        // Volumen y cantidad de transacciones            
        $queryTransactionsVolume = Office::selectRaw('
            offices.name as office_name,
            offices.id as office_id,
            IF('.$data['inDollars'].' = 0, 
                SUM(transactions.current_listing_price),
                SUM(
                    IFNULL(
                        transactions.current_listing_price/exchange_rates.amount, 
                        transactions.current_listing_price/6.96
                    )
                ) 
            ) as transaction_volume,
            COUNT(transactions.id) as transactions,
            YEAR(transactions.sold_date) as year
        ')
        ->join('transactions', 'transactions.office_id', '=', 'offices.id')
        ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'transactions.exchange_rate_id')
        ->whereIn('transactions.transaction_status_id', [2,4,5])
        ->where(function ($q) use ($data) {
            $q->whereYear('transactions.sold_date', $data['year'])
              ->orWhereYear('transactions.sold_date', $data['year'] - 1);
        })
        ->whereIn(DB::raw('MONTH(transactions.sold_date)'), $data['months'])
        ->groupBy('offices.id','offices.name',DB::raw('YEAR(transactions.sold_date)'))
        ->get();

        // Listings activos
        $currentQuery = Office::selectRaw('
                offices.id as office_id,
                offices.name as office_name,
                COUNT(DISTINCT (listings.id)) as active_listings,
                YEAR(?) as year
            ', [$currentDate->toDateString()])
            ->join('agents', 'agents.office_id', '=', 'offices.office_id')
            ->join('agent_listing', 'agent_listing.agent_id', '=', 'agents.id')
            ->join('listings', 'listings.id', '=', 'agent_listing.listing_id')
            ->join('listing_prices', 'listing_prices.listing_id', '=', 'listings.id')
            ->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->where(function ($subquery) {
                $subquery->whereNull('listing_prices.currency_id')
                    ->orWhere('listing_prices.currency_id', 1);
            })
            ->whereRaw('
                LEAST(
                    IFNULL(listings.cancellation_date, "9999-01-01"),
                    IFNULL(listings.contract_end_date, "9999-01-01"),
                    IFNULL(transactions.sold_date, "9999-01-01")
                ) > ?
            ', [$currentDate->toDateString()])
            ->where('listings.date_of_listing', '<=', $currentDate->toDateString())
            ->groupBy('offices.id','offices.name');

        

        $previousQuery = Office::selectRaw('
                offices.id as office_id,
                offices.name as office_name,
                COUNT(DISTINCT(listings.id)) as active_listings,
                YEAR(?) as year
            ', [$previousDate->toDateString()])
            ->join('agents', 'agents.office_id', '=', 'offices.office_id')
            ->join('agent_listing', 'agent_listing.agent_id', '=', 'agents.id')
            ->join('listings', 'listings.id', '=', 'agent_listing.listing_id')
            ->join('listing_prices', 'listing_prices.listing_id', '=', 'listings.id')
            ->leftJoin('transactions', 'transactions.listing_id', '=', 'listings.id')
            ->where(function ($subquery) {
                $subquery->whereNull('listing_prices.currency_id')
                    ->orWhere('listing_prices.currency_id', 1);
            })
            ->whereRaw('
                LEAST(
                    IFNULL(listings.cancellation_date, "9999-01-01"),
                    IFNULL(listings.contract_end_date, "9999-01-01"),
                    IFNULL(transactions.sold_date, "9999-01-01")
                ) > ?
            ', [$previousDate->toDateString()])
            ->where('listings.date_of_listing', '<=', $previousDate->toDateString())
            ->groupBy('offices.id', 'offices.name');

        $listings = $currentQuery->unionAll($previousQuery)->get();


        // Agentes activos
        $queryAgentsActiveToday = Office::selectRaw('
            offices.id as office_id,
            COUNT(agents.id) as agents
        ')
        ->join('agents', 'agents.office_id', '=', 'offices.office_id')
        ->where('agents.agent_status_id',1)
        ->groupBy('offices.id')
        ->get();
        
        $diferenceCurrent = $this->getDiferenceAgentsAllOffices($today, $data['last_date']);
        $diferencePrevious = $this->getDiferenceAgentsAllOffices($today, $previousDate->toDateString());

        foreach($queryAgentsActiveToday as $office) {
            $office->agentsCurrent = $office->agents - $diferenceCurrent->where('id', $office->office_id)->first()->agents;
            $office->agentsPrevious = $office->agents - $diferencePrevious->where('id', $office->office_id)->first()->agents;
        }

        // Total de pagos
        $queryPayments = Office::selectRaw('
            offices.id as office_id,
            IF('.$data['inDollars'].' = 0, 
                SUM(payments.amount_expected),
                SUM(
                    IFNULL(
                        payments.amount_expected/exchange_rates.amount, 
                        payments.amount_expected/6.96
                    )
                ) 
            ) as payment_amount,
            YEAR(payments.expected_payment_date) as year,
            offices.name as office_name
        ')
            ->join('agents', 'agents.office_id', '=', 'offices.office_id')
            ->join('payments', 'payments.agent_internal_id', '=', 'agents.agent_internal_id')
            ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'payments.exchange_rate_id')
            ->join('transactions', 'transactions.id', '=', 'payments.transaction_id')
            ->where(function ($q) use ($data) {
                $q->whereYear('payments.expected_payment_date', $data['year'])
                ->orWhereYear('payments.expected_payment_date', $data['year'] - 1);
            })
            ->whereIn(DB::raw('MONTH(payments.expected_payment_date)'), $data['months'])
            ->whereIn('transactions.transaction_status_id', [2,3,4,5])
            ->where(function ($query) {
                $query->whereNotNull('payments.amount_received')
                    ->where('payments.amount_received', '!=', 0);
            })
            ->groupBy('offices.id', 'year','offices.name')
            ->get();

        $allOffices = Office::selectRaw('
            id,
            name,
            DATEDIFF("'. $data['last_date'] .'", DATE_FORMAT(first_updated_to_web, "%Y-%m-%d")) as age
        ')
            ->where(function ($q) {
                $q->whereNull('is_external')
                    ->orWhere('is_external', 0);
            })
            ->where('active_office', 1)
            ->get();
        
        $goals = Goal::selectRaw('
                SUM(goals.transactions) as transactions,
                IF('.$data['inDollars'].' = 0, 
                    SUM(goals.transaction_volume),
                    SUM(
                        IFNULL(
                            goals.transaction_volume/exchange_rates.amount, 
                            goals.transaction_volume/6.96
                        )
                    ) 
                ) as transaction_volume,
                SUM(goals.new_listings) as active_listings,
                IF('.$data['inDollars'].' = 0, 
                    SUM(goals.payment_amount),
                    SUM(
                        IFNULL(
                            goals.payment_amount/exchange_rates.amount, 
                            goals.payment_amount/6.96
                        )
                    ) 
                ) as payment_amount,
                SUM(goals.new_agents) as active_agents,
                goals.office_id
            ')
            ->leftJoin('exchange_rates', 'exchange_rates.id', '=', 'goals.exchange_rate_id')
            ->where('year', $data['year'])
            ->whereIn('month', $data['months'])
            ->whereNull('agent_id')
            ->groupBy('office_id')
            ->get();
        
        foreach($allOffices as $office) {
            
            $office->current_transaction_volume = $queryTransactionsVolume->where('office_id', $office->id)->where('year', $data['year'])->first()->transaction_volume ?? 0;
            $office->comparation_transaction_volume = $data['comparativeType'] == 'year' ?
                $queryTransactionsVolume->where('office_id', $office->id)->where('year', $data['year']-1)->first()->transaction_volume ?? 0 :
                $goals->where('office_id', $office->id)->first()->transaction_volume ?? 0;
            
            $office->current_transactions = $queryTransactionsVolume->where('office_id', $office->id)->where('year', $data['year'])->first()->transactions ?? 0;
            $office->comparation_transactions = $data['comparativeType'] == 'year' ?
                $queryTransactionsVolume->where('office_id', $office->id)->where('year', $data['year']-1)->first()->transactions ?? 0 :
                $goals->where('office_id', $office->id)->first()->transactions ?? 0;

            $office->current_active_listings =  $listings->where('office_id', $office->id)->where('year', $data['year'])->first()->active_listings ?? 0;
            $office->comparation_active_listings = $data['comparativeType'] == 'year' ? 
                $listings->where('office_id', $office->id)->where('year', $data['year'] - 1)->first()->active_listings ?? 0 :
                $goals->where('office_id', $office->id)->first()->active_listings ?? 0;

            $office->current_agents = $queryAgentsActiveToday->where('office_id', $office->id)->first()->agentsCurrent ?? 0;
            $office->comparation_agents = $data['comparativeType'] == 'year' ?
                $queryAgentsActiveToday->where('office_id', $office->id)->first()->agentsPrevious ?? 0 :
                $goals->where('office_id', $office->id)->first()->active_agents ?? 0;
            
            if(in_array(Auth::user()->roles->first()->name, ['Agente', 'Broker']))
            {
                $office->current_payment_amount = 0;
                $office->comparation_payment_amount = 0;
            }
            else
            {
                $office->current_payment_amount = $queryPayments->where('office_id', $office->id)->where('year', $data['year'])->first()->payment_amount ?? 0;
                $office->comparation_payment_amount = $data['comparativeType'] == 'year' ?
                $queryPayments->where('office_id', $office->id)->where('year', $data['year'] - 1)->first()->payment_amount ?? 0 :
                $goals->where('office_id', $office->id)->first()->payment_amount ?? 0;
            }
            
        }

        $sortedOffices = $allOffices->toArray();
        usort($sortedOffices, function($a, $b) {
            return $b['current_payment_amount'] <=> $a['current_payment_amount'];
        });

        return $sortedOffices;
    }

    public function getDiferenceAgentsAllOffices($endDate = null, $startDate = null) 
    {

        $newAgents =Office::selectRaw('
            offices.id,
            COUNT(agents.id) as agents
        ')
        ->leftJoin('agents', function ($q) use($startDate, $endDate){
            $q->on('offices.office_id', '=', 'agents.office_id')
                ->whereBetween('date_joined', [$startDate, $endDate]);
        })
        ->groupBy('offices.id')
        ->get();

        $terminatedAgents = Office::selectRaw('
            offices.id,
            COUNT(agents.id) as agents
        ')
        ->leftJoin('agents', function ($q) use($startDate, $endDate){
            $q->on('offices.office_id', '=', 'agents.office_id')
                ->whereBetween('date_termination', [$startDate, $endDate]);
        })
        ->groupBy('offices.id')
        ->get();

        foreach($newAgents as $newAgent) {
            $newAgent->agents = $newAgent->agents - $terminatedAgents->where('id', $newAgent->id)->first()->agents;
        }

        return $newAgents;
    }
}