<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::create(['name' => 'Admin']);
        Permission::create(['name' => 'Executivo']);
        Permission::create(['name' => 'Operacao']);
        Permission::create(['name' => 'Analista']);
        Permission::create(['name' => 'Rh']);
        Permission::create(['name' => 'Fin']);
        Permission::create(['name' => 'TI']);
        Permission::create(['name' => 'Geral']);
        Permission::create(['name' => 'Diretoria']);
    }
}
