<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\NomenclatureController;
use App\Http\Controllers\TesteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['handle.cors'])->post('/login', [AuthController::class, 'login']);

//Rotas protegidas pelo middleware sys.auth
Route::middleware(['sec.check', 'handle.cors'])->group(function () {

    //Auth
    Route::get('/refresh', [AuthController::class, 'refresh']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);


    //Colaboradores e Permissões
    Route::middleware('check.permission:Admin, Executivo, Operacao')->get('/colaboradores', [CollaboratorController::class , 'getAllDb']);
    Route::middleware('check.permission:Admin, Executivo, Operacao')->put('/colaborador', [CollaboratorController::class , 'update']);

    //Vincular um colaborador a um contrato
    Route::middleware('check.permission:Admin, Executivo, Operacao')->post('/colaborador/contrato', [ContractController::class , 'collaborator']);

    //Contratos
    Route::middleware('check.permission: Admin, Executivo, Operacao')->get('/contratos', [ContractController::class, 'getAllContracts']);
    Route::middleware('check.permission: Admin, Executivo, Operacao')->put('/contratos', [ContractController::class, 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operacao')->get('/contratos/novos', [ContractController::class, 'updateContracts']);
    
    //CheckList//
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,TI,Geral')->get('/checklist', [ChecklistController::class , 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->get('/checklist/{id}', [ChecklistController::class, 'getbyID']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->post('/checklist',[ChecklistController::class, 'store']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->put('/checklist/{id}', [ChecklistController::class,'update']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->patch('/checklist/{id}', [ChecklistController::class,'update']);


    //Nomenclaturas padrão dos arquivos
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->get('/nomenclatura', [NomenclatureController::class, 'getAll']);
    Route::middleware('check.permission: Admin')->post('/nomenclatura', [NomenclatureController::class, 'create']);
    Route::middleware('check.permission: Admin')->put('/nomenclatura', [NomenclatureController::class, 'update']);
    Route::middleware('check.permission: Admin')->delete('/nomenclatura', [NomenclatureController::class, 'delete']);

    //Itens
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->get('/item', [ItemController::class, 'show']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->get('/item/{id}', [ItemController::class, 'findOne']);
    Route::middleware('check.permission: Admin')->post('/item', [ItemController::class, 'update']);
    Route::middleware('check.permission: Admin')->patch('/item/{id}', [ItemController::class, 'update']);
    Route::middleware('check.permission: Admin')->patch('/item/{id}', [ItemController::class, 'delete']);
    
  
});


Route::middleware('sys.auth')->get('/teste', [TesteController::class, 'novoteste']);






Route::get('/documentation', function () {
    return view('documentation');
});

Route::fallback(function () {
    return response()->json(['error' => 'Recurso não encontrado!'], 404);
});
