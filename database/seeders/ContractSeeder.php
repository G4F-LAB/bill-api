<?php

namespace Database\Seeders;

use App\Models\Contract;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('contracts')->insert([
            [
                'id_contrato' => '3125',
                'name' => 'Contrato 1',
                'contractual_situation' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_contrato' => '8552',
                'name' => 'Contrato 2',
                'contractual_situation' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Adicione mais registros conforme necessário
        ]);
    }
}
