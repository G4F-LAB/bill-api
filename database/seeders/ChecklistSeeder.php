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
                'date_checklist' => \DB::raw("TO_DATE('09-2023', 'MM-YYYY')"),
                'object_contract' => 'hsajfh',
                'shipping_method' => 'email',
                'obs' => 'teste',
                'accept' => false,
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '1',
                'date_checklist' => \DB::raw("TO_DATE('10-2023', 'MM-YYYY')"),
                'object_contract' => 'teste-controller',
                'shipping_method' => 'teste',
                'obs' => 'teste',
                'accept' => true,
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '138',
                'date_checklist' => \DB::raw("TO_DATE('11-2023', 'MM-YYYY')"),
                'object_contract' => 'teste-controller',
                'shipping_method' => 'teste',
                'obs' => 'teste',
                'accept' => true,
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '138',
                'date_checklist' => \DB::raw("TO_DATE('12-2023', 'MM-YYYY')"),
                'object_contract' => 'teste-controller',
                'shipping_method' => 'teste',
                'obs' => 'teste',
                'accept' => true,
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '104',
                'date_checklist' => \DB::raw("TO_DATE('12-2023', 'MM-YYYY')"),
                'object_contract' => 'teste-controller',
                'shipping_method' => 'teste',
                'obs' => 'teste',
                'accept' => true,
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract_id' => '131',
                'date_checklist' => \DB::raw("TO_DATE('12-2023', 'MM-YYYY')"),
                'object_contract' => 'teste-controller',
                'shipping_method' => 'teste',
                'obs' => 'teste',
                'accept' => true,
                'signed_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
    
