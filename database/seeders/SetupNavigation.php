<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetupNavigation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table("setup_navigations")->insert([
            [
                "parent_id"=>NULL,
                "name"=> "Dashboard",
                "slug"=>NULL,
                "icon"=>"ant-design:home-outlined",
                "path"=> "/analytics",
                "sort"=> 2,
                "permissions_id"=> "[1,2,3,4,5,6,7,8]",
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
                "permissions_id"=> "[1,7,8]",
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
                "permissions_id"=> "[1,3,7,8]",
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
                "permissions_id"=> "[1,3,7,8]",
                "created_at"=> now(),
                "updated_at"=> now(),
            ],
        ]);
    }
}
