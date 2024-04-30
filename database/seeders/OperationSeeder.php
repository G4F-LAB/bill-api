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
                "reference"=>100,
                "executive_id"=> 1,
            ],
            [
                "name"=> "Operação 2",
                "reference"=>200,
                "executive_id"=> 2,
            ],
            [
                "name"=> "Operação 3",
                "reference"=>300,
                "executive_id"=> 1,
            ],
            [
                "name"=> "Operação 4",
                "reference"=>400,
                "executive_id"=> 2,
            ],
            [
                "name"=> "Operação 5",
                "reference"=>500,
                "executive_id"=> 2,
            ],
            [
                "name"=> "Operação 6",
                "reference"=>600,
                "executive_id"=> 1,
            ],
            [
                "name"=> "Operação 7",
                "reference"=>700,
                "executive_id"=> 2,
            ],
            [
                "name"=> "Operação 8",
                "reference"=>800,
                "executive_id"=> 1,
            ],
            [
                "name"=> "Operação 9",
                "reference"=>900,
                "executive_id"=> 1,
            ],
            [
                "name"=> "Operação 10",
                "reference"=>910,
                "executive_id"=> 2,
            ],

        ]);
    }
}
