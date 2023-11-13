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
                'id_contrato' => 'cto-4',
                'data_checklist' => $data_checklist,
                'objeto_contrato' => 'hsajfh',
                'forma_envio' => 'email',
                'obs' => 'teste',
                'aceite' => false,
                'setor' => 'Gerencia',
                'assinado_por' => 'Eduardo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_contrato' => 'cto-5',
                'data_checklist' => $data_checklist,
                'objeto_contrato' => 'teste',
                'forma_envio' => 'teste',
                'obs' => 'teste',
                'aceite' => true,
                'setor' => 'RH',
                'assinado_por' => 'Eduardo.Borges',
                'created_at' => now(),
                'updated_at' => now(),
            ]
            // Adicione mais registros conforme necessário
        ]);
    }
}
    
