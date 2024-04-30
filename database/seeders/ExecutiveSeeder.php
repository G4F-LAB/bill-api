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
                'created_at'=> now()->toDateTimeString(),
                'updated_at'=> now()->toDateTimeString(),
            ],
            [
                'name'=> 'Gerência Executiva 2',
                'created_at'=> now()->toDateTimeString(),
                'updated_at'=> now()->toDateTimeString(),
            ],
        ]);
    }
}
