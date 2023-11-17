<?php

namespace Database\Seeders;

use App\Models\File_type;
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
                "categoria_arquivo" => "Recursos Humanos"
            ],
            [
                "categoria_arquivo" => "Financeiro"
            ],
            [
                "categoria_arquivo" => "Operação"
            ],
        ];

        foreach ($dados as $dado) {
            File_type::create($dado);
        }
    }
}
