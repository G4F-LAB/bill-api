<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExecutiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        DB::table('executives')->insert([
            [
                'name'=> 'Gerência Executiva 1',
                'operations'=>[1,3,6,8,9],
                'created_at'=> now()->toDateTimeString(),
                'updated_at'=> now()->toDateTimeString(),
            ],
            [
                'name'=> 'Gerência Executiva 2',
                'operations'=>[2,4,5,7,10],
                'created_at'=> now()->toDateTimeString(),
                'updated_at'=> now()->toDateTimeString(),
            ],
        ]);
    }
}
