<?php

namespace Database\Seeders;

use App\Models\TransactionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $status_list = [
            'Canceled',
            'Submitted',
            'Incompleto',
            'Pendiente de aprobación',
            'Aceptado',
            'No Aceptado'
        ];

        foreach($status_list as $status){
            TransactionStatus::create([
                'name' => $status
            ]);
        }
    }
}
