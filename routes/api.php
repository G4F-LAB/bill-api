<?php

use App\Http\Controllers\ContractDateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileNamingController;
use App\Http\Controllers\TesteController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\ExecutiveController;
use App\Http\Controllers\FileCompetenceController;
use App\Http\Controllers\OperationController;

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
// Route::get('/nomenclatura', [FileNamingController::class, 'getAll']);

//Rotas protegidas pelo middleware sys.auth
Route::middleware(['sec.check', 'handle.cors', 'sys.auth'])->group(function () {

    //Auth
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/refresh', [AuthController::class, 'refresh']);
    Route::put('/update_info', [AuthController::class, 'update_info']);

    //Setup
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,TI,Geral')->get('/setup/navigation', [SetupController::class, 'navigation']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,TI,Geral')->post('/setup/navigation', [SetupController::class, 'navigation_upsert']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,TI,Geral')->delete('/setup/navigation/{id}', [SetupController::class, 'navigation_delete']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,TI,Geral')->get('/setup/permissions', [SetupController::class, 'permissions']);


    //Colaboradores e Permissões
    Route::middleware('check.permission: Admin, Executivo, Operacao, TI')->get('/colaboradores', [CollaboratorController::class , 'getAllDb']);
    Route::middleware('check.permission: Admin, Executivo, Operacao, TI')->get('/colaboradores/manager', [CollaboratorController::class , 'getAllManagers']);
    Route::middleware('check.permission: Admin, Executivo, Operacao, TI')->put('/collaborators', [CollaboratorController::class , 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operacao, TI')->get('/collaborators/permissions', [CollaboratorController::class , 'collaboratorsByPermission']);
    Route::middleware('check.permission: Admin, Executivo, TI')->post('/collaborators/create', [CollaboratorController::class , 'create']);

    //Vincular/desvincular um colaborador a uma operação
    Route::middleware('check.permission: Admin, Executivo, Operacao')->post('/collaborator/operation', [CollaboratorController::class , 'collaboratorOperation']);
    Route::middleware('check.permission: Admin, Executivo, Operacao')->post('/collaborator/oper', [CollaboratorController::class , 'unlinkCollaborator']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->get('/collaborators/typescontracts', [ContractController::class , 'getContractsOfCollab']);


    //Contratos
    Route::middleware('check.permission: Admin, Executivo, Operacao, Analista')->get('/contracts', [ContractController::class, 'getAllContracts']);
    Route::middleware('check.permission: Admin, Executivo, Operacao, Analista')->put('/contracts', [ContractController::class, 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operacao, Analista')->post('/contracts/update', [ContractController::class, 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operacao')->post('/contracts/new', [ContractController::class, 'updateListContracts']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->get('/contracts/{id}/checklist', [ContractController::class, 'checklistByContractID']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->post('/default/files', [FileController::class,'uploadDefaultFiles']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->post('/contract/ocurrences', [FileController::class,'searchOcurrence']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->post('/contract/files', [FileController::class,'uploadContractFiles']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->post('/contract/create', [ContractController::class,'createContract']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->post('/contract/inactive', [ContractController::class,'inactiveContract']);

    //CheckList//
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,TI,Geral')->get('/checklist', [ChecklistController::class , 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->post('/checklist',[ChecklistController::class, 'store']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->put('/checklist/{id}', [ChecklistController::class,'update']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->patch('/checklist/{id}', [ChecklistController::class,'update']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->post('/checklist/{id}/files', [FileController::class,'uploadChecklistFiles']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->get('/checklist/{id}/items', [ChecklistController::class,'checklistItens']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista')->get('/checklist/{id}/items/create', [ChecklistController::class,'checklistItensCreate']);
    Route::middleware('check.permission:Admin,Executivo,Operacao,Analista')->get('/checklist/{id}/filter', [ContractDateController::class,'getListChecklist']);
    Route::middleware('check.permission:Admin,Executivo,Operacao,Analista')->get('/checklist/{id}/{reference}', [ChecklistController::class,'getDataChecklist']);
    Route::middleware('check.permission:Admin,Executivo,Operacao,Analista')->get('/competence', [ChecklistController::class,'getAllCompetence']);
    Route::middleware('check.permission:Admin,Executivo,Operacao,Analista')->get('/check/checklist', [ChecklistController::class,'checkChelistExpired']);
    //automate
    Route::middleware('check.permission:Admin,Executivo,Operacao,Analista')->get('/automate/checklist/items/duplicateall', [ChecklistController::class,'duplicateall']);

    //Analytics
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/analytics',[AnalyticsController::class,'getMyAnalytics']);
    Route::middleware('check.permission: Admin')->get('/directory',[DirectoryController::class,'getAnalyticsDirectory']);
    // Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/analytics/{id}',[AnalyticsController::class,'getMyAnalytics']);

    //Nomenclaturas padrão dos arquivos
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->get('/filenaming', [FileNamingController::class, 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->get('/filenaming/{id}', [FileNamingController::class, 'getByID']);
    Route::middleware('check.permission: Admin')->post('/filenaming', [FileNamingController::class, 'create']);
    Route::middleware('check.permission: Admin')->put('/filenaming/{id}', [FileNamingController::class, 'update']);
    Route::middleware('check.permission: Admin')->delete('/filenaming', [FileNamingController::class, 'delete']);
    Route::middleware('check.permission: Admin')->get('/filenaming/checklist/{id}', [FileNamingController::class, 'getAllRelCheklist']);

    //Itens
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->get('/item', [ItemController::class, 'show']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->get('/item/{id}', [ItemController::class, 'getbyID']);
    Route::middleware('check.permission: Admin')->post('/item', [ItemController::class, 'store']);
    Route::middleware('check.permission: Admin')->put('/item/{id}', [ItemController::class, 'update']);
    Route::middleware('check.permission: Admin')->put('/item/competence/{id}', [ItemController::class, 'updateCompetence']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->delete('/item/{id}', [ItemController::class, 'destroy']);
    Route::middleware('check.permission:Admin,Executivo,Operacao,Analista')->get('/itens/export', [ItemController::class,'exportFiles']);
    Route::middleware('check.permission:Admin,Executivo,Operacao,Analista')->post('/update/competence/{id}', [ItemController::class,'updateCompetence']);

    //Operações
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/operacoes', [OperationController::class, 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->delete('/operacoes/{id}', [OperationController::class, 'delete']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->post('/operacoes', [OperationController::class, 'create']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->post('/operacoes/{id}', [OperationController::class, 'update']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/operations', [OperationController::class, 'getAllOperations']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/operations/managers', [OperationController::class, 'getAllManagerofOperation']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->post('/collab_operacoes', [OperationController::class, 'deleteCollabOperation']);

    //Executivo
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/executivo', [ExecutiveController::class, 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/executivo/manager', [ExecutiveController::class, 'getById']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/executives', [ExecutiveController::class, 'getAllExecutives']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->get('/executivo/allmanager', [ExecutiveController::class, 'getAllManager']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->delete('/executivo/{id}', [ExecutiveController::class, 'delete']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->post('/executivo', [ExecutiveController::class, 'create']);
    Route::middleware('check.permission: Admin,Executivo,Operacao')->post('/executivo/{id}', [ExecutiveController::class, 'update']);

    //Competencia
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin')->get('/competencias', [FileCompetenceController::class, 'getAll']);


    //LOG
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->get('/log', [LogController::class, 'show']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->post('/log/contract', [LogController::class,'getLogName']);
    Route::middleware('check.permission: Admin,Executivo,Operacao,Analista,Rh,Fin,Geral')->post('/log/collaborator', [LogController::class, 'getLogCollaborator']);

    //Automação
    Route::middleware('check.permission: Admin')->post('/automacao', [FileController::class, 'automacao']);

    Route::middleware('check.permission:All')->delete('/file/{file_id}/{item_id}', [FileController::class, 'deleteFile']);

});


Route::middleware('sys.auth')->get('/teste', [TesteController::class, 'novoteste2']);
Route::post('/teste', [TesteController::class, 'novoteste']);
Route::get('/teste2', [TesteController::class, 'novoteste2']);






Route::get('/documentation', function () {
    return view('documentation');
});

Route::fallback(function () {
    return response()->json(['error' => 'Recurso não encontrado!'], 404);
});
