<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table("operations")->insert([
            [
                "name"=> "Operação 1",
            ],
            [
                "name"=> "Operação 2",
            ],
            [
                "name"=> "Operação 3",
            ],
            [
                "name"=> "Operação 4",
            ],
            [
                "name"=> "Operação 5",
            ],
            [
                "name"=> "Operação 6",
            ],

        ]);
    }
}
