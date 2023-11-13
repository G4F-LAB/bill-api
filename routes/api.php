<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ChecklistController;
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

Route::middleware(['sec.check', 'handle.cors'])->post('/login', [AuthController::class, 'login']);

//Rotas protegidas pelo middleware sys.auth
Route::middleware(['sys.auth', 'sec.check', 'handle.cors'])->group(function () {

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
    

});


Route::post('/teste', [TesteController::class, 'teste']);


//CheckList//
Route::get('/checklist', [ChecklistController::class , 'getAll']);
Route::get('/checklist/{id}', [ChecklistController::class, 'getbyID']);
Route::post('/checklist',[ChecklistController::class, 'store']);

Route::get('/documentation', function () {
    return view('documentation');
});

Route::fallback(function () {
    return response()->json(['error' => 'Recurso não encontrado!'], 404);
});
