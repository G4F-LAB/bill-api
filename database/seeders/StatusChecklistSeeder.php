<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table("status_checklist")->insert([
            [
                "name"=> "Iniciado",
            ],
            [
                "name"=> "Em progresso",
            ],
            [
                "name"=> "Assinatura pendente",
            ],
            [
                "name"=> "Validação pendente",
            ],
            [
                "name"=> "Finalizado",
            ],
        ]);
    }
}
