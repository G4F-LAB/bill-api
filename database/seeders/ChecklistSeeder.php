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
        \DB::table('checklists')->insert([
            [
                'contract' => 'cto-4',
                'date_checklist' => $data_checklist,
                'object_contract' => 'hsajfh',
                'shipping_method' => 'email',
                'obs' => 'teste',
                'accept' => false,
                'sector' => 'Gerencia',
                'signed_by' => 'Eduardo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'contract' => 'cto-5',
                'date_checklist' => $data_checklist,
                'object_contract' => 'teste',
                'shipping_method' => 'teste',
                'obs' => 'teste',
                'accept' => true,
                'sector' => 'RH',
                'signed_by' => 'Eduardo.Borges',
                'created_at' => now(),
                'updated_at' => now(),
            ]
            // Adicione mais registros conforme necessário
        ]);
    }
}
    
