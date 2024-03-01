<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('itens')->insert([
            [
                'checklist_id' => '7',
                'file_naming_id' => '1',
                'file_type_id' => '1',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '7',
                'file_naming_id' => '2',
                'file_type_id' => '1',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '7',
                'file_naming_id' => '3',
                'file_type_id' => '1',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '7',
                'file_naming_id' => '1',
                'file_type_id' => '2',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '7',
                'file_naming_id' => '2',
                'file_type_id' => '2',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'checklist_id' => '8',
                'file_naming_id' => '1',
                'file_type_id' => '1',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '8',
                'file_naming_id' => '2',
                'file_type_id' => '1',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '8',
                'file_naming_id' => '3',
                'file_type_id' => '1',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '8',
                'file_naming_id' => '1',
                'file_type_id' => '2',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'checklist_id' => '8',
                'file_naming_id' => '2',
                'file_type_id' => '2',
                'status' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
