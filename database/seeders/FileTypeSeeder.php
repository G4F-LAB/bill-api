<?php

namespace Database\Seeders;

use App\Models\FileType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FileTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dados = [
            [
                "files_category" => "Recursos Humanos"
            ],
            [
                "files_category" => "Financeiro"
            ],
            [
                "files_category" => "Operação"
            ],
        ];

        foreach ($dados as $dado) {
            FileType::create($dado);
        }
    }
}
