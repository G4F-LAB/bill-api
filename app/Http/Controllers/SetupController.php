<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SetupNavigation;
use App\Models\Collaborator;
use App\Models\Permission;
use Illuminate\Support\Facades\Auth;



class SetupController extends Controller
{
    public function __construct(Collaborator $collaborator) {
        $this->env = env('APP_ENV') ? env('APP_ENV') : 'developer';
        $this->auth_user = $collaborator->getAuthUser();

    }

    // Menus
    function navigation(Request $request) {
        
        $colaborador = $this->auth_user;

        $menu = SetupNavigation::whereJsonContains('permission_ids', [$colaborador->type])->where('parent_id', NULL)->orderBy('sort', 'asc')->get();
        $data = array();
        foreach ($menu as $index => $item) {

            $form_data = [
                "id" => $item->id,
                "title" => $item->name,
                "slug" => $item->slug,
                "path" => $item->path,
                "icon" => $item->icon,
                "sort" => $item->sort,
                "permission_ids" => $item->permission_ids,
                "description" => $item->description
            ];

            $childrens = SetupNavigation::where('parent_id', $item->id)->whereJsonContains('permission_ids', [$colaborador->permission_id])->orderBy('sort', 'asc')->get();
            if(isset($childrens) && count($childrens) > 0  ){
                $c_data = array("children" => $childrens);
                $form_data = array_merge($form_data, $c_data);
            }
            array_push($data, $form_data);
        }

        return $data;
    }
    function navigation_upsert(Request $request) {

        $slug = $request->slug;
        $name = $request->title;
        $parent_id = $request->parent_id ? (int) $request->parent_id : NULL;
        $path = $request->path;
        $icon = $request->icon;
        $sort = $request->sort;
        $description = $request->description;

        $permission_ids = array();
        if(is_array($request->permission_ids)){
            foreach ($request->permission_ids as $item) {
            if (is_numeric($item)) { array_push($permission_ids, (int)$item); }
            }
        }else{
            $permission_ids = $request->permission_ids;
        }

        try {
            $menu = SetupNavigation::updateOrCreate(
                ['slug' => $slug, 'name' => $name, 'parent_id' => $parent_id],
            );

            if(isset($path)){
                $menu->path = $path;
            }
            if(isset($icon)){
                $menu->icon = $icon;
            }
            if(isset($sort)){
                $menu->sort = $sort;
            }
            if(isset($permission_ids)){
                $menu->permission_ids = $permission_ids;
            }
            if(isset($description)){
                $menu->description = $description;
            }

            $menu->path = $path;
            $menu->save();
        } catch (\Throwable $th) {
            throw $th;
            return response()->json(['erro'=> $th],500);
        }
      return response()->json($menu, 200);
    }

    public function navigation_delete(Request $request)
    {
        try {

            $menu = SetupNavigation::find($request->id);

            if (!$menu)
                return response()->json(['error' => 'Menu não encontrado'], 404);

            $menu->delete();

            return response()->json(['message' => 'Menu excluído!']);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
        }
    }

    public function permissions()
    {
        try {

            $permissions = Permission::orderBy('id', 'asc')->get();

            if (!$permissions)
                return response()->json(['error' => 'Menu não encontrado'], 404);



            return response()->json($permissions);

        } catch (\Exception $exception) {
            return response()->json(['error' => 'Não foi possível atualizar, tente novamente mais tarde.'], 500);
        }
    }

}



