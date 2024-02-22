<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SetupNavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("setup_navigations")->insert([
            [
                "parent_id"=>NULL,
                "name"=> "Dashboard",
                "slug"=>NULL,
                "icon"=>"ant-design:home-outlined",
                "path"=> "/analytics",
                "sort"=> 2,
                "permission_ids"=> "[1,2,3,4,5,6,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>NULL,
                "name"=> "Colaboradores",
                "slug"=>NULL,
                "icon"=>"fa-solid:users",
                "path"=> "/collaborators",
                "sort"=> 5,
                "permission_ids"=> "[1,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>2,
                "name"=> "Permissões",
                "slug"=>NULL,
                "icon"=>NULL,
                "path"=> "/collaborators/permissions",
                "sort"=> 5,
                "permission_ids"=> "[1,3,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>2,
                "name"=> "Lista",
                "slug"=>NULL,
                "icon"=>NULL,
                "path"=> "/collaborators",
                "sort"=> 0,
                "permission_ids"=> "[1,3,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>NULL,
                "name"=> "Contratos",
                "slug"=>NULL,
                "icon"=> "clarity:contract-line",
                "path"=> "/contracts",
                "sort"=> 20,
                "permission_ids"=> "[1,2,3,4,5,6,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>5,
                "name"=> "Lista",
                "slug"=>NULL,
                "icon"=> NULL,
                "path"=> "/contracts",
                "sort"=> 25,
                "permission_ids"=> "[1,2,3,4,5,6,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>5,
                "name"=> "Subir Arquivos",
                "slug"=>NULL,
                "icon"=> NULL,
                "path"=> "/contracts/uploads",
                "sort"=> 25,
                "permission_ids"=> "[1,2,3,4,5,6,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>null,
                "name"=> "Admin",
                "slug"=>NULL,
                "icon"=> "eos-icons:admin-outlined",
                "path"=> "/admin",
                "sort"=> 21,
                "permission_ids"=> "[1]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
            [
                "parent_id"=>null,
                "name"=> "Admin",
                "slug"=>NULL,
                "icon"=> NULL,
                "path"=> NULL,
                "sort"=> 0,
                "permission_ids"=> NULL,
                "created_at"=> now(),
                "updated_at"=> now(),
            ],

        ]);
    }
}
