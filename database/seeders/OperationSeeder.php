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
                "reference"=>100
            ],
            [
                "name"=> "Operação 2",
                "reference"=>200
            ],
            [
                "name"=> "Operação 3",
                "reference"=>300
            ],
            [
                "name"=> "Operação 4",
                "reference"=>400
            ],
            [
                "name"=> "Operação 5",
                "reference"=>500
            ],
            [
                "name"=> "Operação 6",
                "reference"=>600
            ],
            [
                "name"=> "Operação 7",
                "reference"=>700
            ],
            [
                "name"=> "Operação 8",
                "reference"=>800
            ],
            [
                "name"=> "Operação 9",
                "reference"=>900
            ],
            [
                "name"=> "Operação 10",
                "reference"=>910
            ],

        ]);
    }
}
