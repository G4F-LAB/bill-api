<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    
    {
        $data_checklist = "2023-10-20";
        $data_checklist1 = "2023-09-10";
        \DB::table('checklists')->insert([
            [
                'contract_id' => '1',
                'date_checklist' => $data_checklist,
                'object_contract' => 'hsajfh',
                'shipping_method' => 'email',
                'obs' => 'teste',
                'accept' => false,
                'sector' => 'teste',
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '1',
                'date_checklist' => $data_checklist1,
                'object_contract' => 'teste-controller',
                'shipping_method' => 'teste',
                'obs' => 'teste',
                'accept' => true,
                'sector' => 'teste-3',
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
    
