<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileCompetenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table("file_competences")->insert([
            [
                "competence"=> "referente ao mês anterior",
            ],
            [
                "competence"=> "referente ao mês da prestação de serviço",
            ]
        ]);
    }
}
