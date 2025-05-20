<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeAdditionalPayments;

class TypeAdditionalPaymentsSeeder extends Seeder
{
    public function run()
    {
        $payments = [
            'Parking',
            'Heating',
            'Utilities',
            'Other',
            'Home Owners Association',
            'Transaction Coordinator',
            'Legal Services',
            'Government Fee',
            'Leasehold Land Fee per Month',
            'Leasehold Guarantor Money',
            'Leasehold Right Money',
            'Levies',
            'Memberships',
            'Land Tax',
            'Waste Management Fee',
            'Marina Fee',
            'Fire Department Fee',
            'Water',
            'Electric',
        ];

        foreach ($payments as $payment) {
            TypeAdditionalPayments::create(['name' => $payment]);
        }
    }
}
