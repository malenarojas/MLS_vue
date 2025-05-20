<?php 

namespace App\Services;

use App\Models\Agent;
use App\Models\Goal;
use App\Models\Office;

class GoalService {

    public function getGoals ($data) {
        // Metas de los agentes de una oficina
        if(isset($data['office_id']) && $data['office_id'] != '') {
            $goals = $this->getGoalsByOffice($data['office_id'], $data['month'], $data['year']);
        }else{
            // Metas de las oficinas
            $goals = $this->getGoalsByRegion($data['month'], $data['year']);
        }

        return $goals;
    }

    public function getGoalsByRegion($month, $year){
        $query = Office::selectRaw('
            offices.name as office_name,
            offices.id as office_id,
            goals.new_contacts,
            goals.new_agents,
            goals.transactions,
            goals.payment_amount,
            goals.new_listings,
            goals.id,
            goals.transaction_volume,
            goals.time_in_market,
            goals.production_by_agent
        ')
        ->leftJoin('goals', function ($query) use ($month, $year) {
            $query->on('goals.office_id', '=', 'offices.id')
                 ->whereNull('goals.agent_id')
                 ->where(function ($query) use ($month) {
                     $query->where('goals.month', $month)
                           ->orWhereNull('goals.month');
                 })
                 ->where(function ($query) use ($year) {
                     $query->where('goals.year', $year)
                           ->orWhereNull('goals.year');
                 });
        })
        ->where('offices.active_office', 1)
        ->whereNull('goals.agent_id')
        ->where(function($query) {
            $query->whereNull('offices.is_external')
                ->orWhere('offices.is_external', 0);
        })
        ->orderBy('offices.name', 'asc');

        return $query->get();
    }

    public function getGoalsByOffice ($office_id, $month, $year) {
        $query = Agent::selectRaw('
            users.name_to_show as agent_name,
            offices.id as office_id,
            agents.id as agent_id,
            goals.new_contacts,
            goals.transactions,
            goals.payment_amount,
            goals.new_listings,
            goals.transaction_volume,
            goals.id,
            goals.time_in_market
        ')
        ->leftJoin('goals', function ($query) use ($month, $year) {
            $query->on('goals.agent_id', '=', 'agents.id')
                ->where(function ($query) use ($month) {
                    $query->where('goals.month', $month)
                        ->orWhere('goals.month', null);
                })
                ->where(function ($query) use ($year) {
                    $query->where('goals.year', $year)
                        ->orWhere('goals.year', null);
                });
        })
        ->join('users', 'users.id', '=', 'agents.user_id')
        ->join('offices', 'offices.office_id', '=', 'agents.office_id')
        ->where('offices.id', $office_id)
        ->where('agents.agent_status_id', 1)
        
        ->orderBy('users.name_to_show', 'asc');

        return $query->get();
    }
}