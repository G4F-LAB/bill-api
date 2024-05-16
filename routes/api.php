<?php

use App\Http\Controllers\ContractDateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileNamingController;
use App\Http\Controllers\FileNameController;
use App\Http\Controllers\TesteController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\TimelineController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\ExecutiveController;
use App\Http\Controllers\FileCompetenceController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperationManagerController;

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

Route::middleware(['sec.check', 'sys.auth', 'handle.cors'])->group(function () {

    $all_permissions = 'check.permission: Admin, Diretoria, Superintendência, Executivo, Gerente, Operação, RH, Financeiro, Processos, TI, Colaborador, Geral';
    //Auth
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/refresh', [AuthController::class, 'refresh']);
    Route::put('/update_info', [AuthController::class, 'update_info']);

    //Setup
    Route::middleware($all_permissions)->get('/setup/navigation', [SetupController::class, 'navigation']);
    Route::middleware($all_permissions)->post('/setup/navigation', [SetupController::class, 'navigation_upsert']);
    Route::middleware($all_permissions)->delete('/setup/navigation/{id}', [SetupController::class, 'navigation_delete']);
    Route::middleware($all_permissions)->get('/setup/permissions', [SetupController::class, 'permissions']);


    //Colaboradores e Permissões
    Route::middleware('check.permission: Admin, Executivo, Operação, TI,Processos')->get('/colaboradores', [CollaboratorController::class , 'getAllDb']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI,Processos')->get('/colaboradores/manager', [CollaboratorController::class , 'getAllManagers']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI,Processos')->put('/collaborators', [CollaboratorController::class , 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI,Processos')->get('/collaborators/permissions', [CollaboratorController::class , 'collaboratorsByPermission']);
    Route::middleware('check.permission: Admin, Executivo, TI')->post('/collaborators/create', [CollaboratorController::class , 'create']);

    //Users
    Route::middleware($all_permissions)->get('/users', [UserController::class , 'index']);
    Route::middleware($all_permissions)->get('/users', [UserController::class , 'index']);
    Route::middleware($all_permissions)->get('/users/types', [UserController::class , 'getUsersGroupedByType']);
    // Route::middleware($all_permissions)->get('/users/birthdays', [UserController::class , 'birthdays']);

    //Vincular/desvincular um colaborador a uma operação
    Route::middleware('check.permission: Admin, Executivo, Operação')->post('/collaborator/operation', [CollaboratorController::class , 'collaboratorOperation']);
    Route::middleware('check.permission: Admin, Executivo, Operação')->post('/collaborator/oper', [CollaboratorController::class , 'unlinkCollaborator']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral,Processos')->get('/collaborators/typescontracts', [ContractController::class , 'getContractsOfCollab']);


    //Contratos
    Route::middleware($all_permissions)->get('/contracts', [ContractController::class, 'index']);
    Route::middleware($all_permissions)->put('/contracts', [ContractController::class, 'update']);
    Route::middleware($all_permissions)->post('/contracts/update', [ContractController::class, 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operação')->post('/contracts/new', [ContractController::class, 'updateListContracts']);
    Route::middleware($all_permissions)->get('/contracts/{id}/checklist', [ContractController::class, 'checklistByContractID']);
    Route::middleware($all_permissions)->post('/default/files', [FileController::class,'uploadDefaultFiles']);
    Route::middleware($all_permissions)->post('/contract/ocurrences', [FileController::class,'searchOcurrence']);
    Route::middleware($all_permissions)->post('/contract/files', [FileController::class,'uploadContractFiles']);
    Route::middleware($all_permissions)->post('/contract/create', [ContractController::class,'createContract']);

    //CheckList//
    Route::middleware($all_permissions)->get('/checklists/{id}', [ChecklistController::class,'show']);
    Route::middleware($all_permissions)->get('/checklist/updateContractId', [ChecklistController::class , 'updateContactIds']);
    Route::middleware($all_permissions)->get('/checklist', [ChecklistController::class , 'getAll']);
    Route::middleware($all_permissions)->post('/checklist',[ChecklistController::class, 'store']);
    Route::middleware($all_permissions)->post('/checklist/{id}', [ChecklistController::class,'update']);
    Route::middleware($all_permissions)->patch('/checklist/{id}', [ChecklistController::class,'update']);
    Route::middleware($all_permissions)->post('/checklist/{id}/files', [FileController::class,'uploadChecklistFiles']);
    Route::middleware($all_permissions)->get('/checklist/{id}/items', [ChecklistController::class,'checklistItens']);
    Route::middleware($all_permissions)->get('/checklist/{id}/items/create', [ChecklistController::class,'checklistItensCreate']);
    Route::middleware($all_permissions)->get('/checklist/{id}/filter', [ContractDateController::class,'getListChecklist']);
    Route::middleware($all_permissions)->get('/checklist/{id}/{reference}', [ChecklistController::class,'getDataChecklist']);
    Route::middleware($all_permissions)->get('/competence', [ChecklistController::class,'getAllCompetence']);
    Route::middleware($all_permissions)->get('/check/checklist', [ChecklistController::class,'checkChecklistExpired']);
    //automate
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista,Processos')->get('/automate/checklist/items/duplicateall', [ChecklistController::class,'duplicateall']);







//RAFAEL / WESLEY
    Route::middleware($all_permissions)->get('/contract/checklist/{id}', [AnalyticsController::class,'getAllChecklist']);
    Route::middleware($all_permissions)->get('/analytics/contract/checklist/teste/{id}', [AnalyticsController::class,'getAllChecklist']);
    Route::middleware($all_permissions)->get('/analytics/checklist/completion/{id}', [AnalyticsController::class,'getCompletion']);
//RAFAEL / WESLEY









      //Analytics
      Route::prefix('/analytics')->group(function () {
        Route::middleware('check.permission: Admin,Executivo,Operação,Processos')->get('/operations', [AnalyticsController::class,'operation_data']);
        Route::middleware('check.permission: Admin,Executivo,Operação,Processos')->get('/operations/{id}/contracts', [AnalyticsController::class,'contracts']);
        Route::middleware('check.permission: Admin,Executivo,Operação,Processos')->get('/contracts/{id}/collaborators', [AnalyticsController::class,'collaborators']);
    });
















    Route::middleware('check.permission: Admin')->get('/directory',[DirectoryController::class,'getAnalyticsDirectory']);
    // Route::middleware('check.permission: Admin,Executivo,Operação')->get('/analytics/{id}',[AnalyticsController::class,'getMyAnalytics']);

    //Nomenclaturas padrão dos arquivos

    Route::middleware($all_permissions)->prefix('filenames')->group(function () {
        Route::get('/', [FileNameController::class, 'index']);
        Route::post('/', [FileNameController::class, 'create']);
        Route::get('/{id}', [FileNameController::class, 'get']);
        Route::put('/{id}', [FileNameController::class, 'update']);
        Route::delete('/{id}', [FileNameController::class, 'delete']);

    });

    Route::middleware($all_permissions)->get('/filenaming', [FileNamingController::class, 'getAll']);
    Route::middleware($all_permissions)->get('/filenaming/{id}', [FileNamingController::class, 'getByID']);
    Route::middleware($all_permissions)->post('/filenaming', [FileNamingController::class, 'store']);
    Route::middleware($all_permissions)->put('/filenaming/{id}', [FileNamingController::class, 'update']);
    Route::middleware($all_permissions)->delete('/filenaming', [FileNamingController::class, 'delete']);
    Route::middleware($all_permissions)->get('/filenaming/checklist/{id}', [FileNamingController::class, 'getAllRelCheklist']);
    Route::middleware($all_permissions)->get('/filetypes', [FileNamingController::class, 'getFileCatogary']);

    //Itens
    Route::middleware($all_permissions)->get('/item', [ItemController::class, 'show']);
    Route::middleware($all_permissions)->get('/item/{id}', [ItemController::class, 'getbyID']);
    Route::middleware($all_permissions)->post('/item', [ItemController::class, 'store']);
    Route::middleware($all_permissions)->put('/item/{id}', [ItemController::class, 'update']);
    Route::middleware($all_permissions)->put('/item/competence/{id}', [ItemController::class, 'updateCompetence']);
    Route::middleware($all_permissions)->delete('/item/{id}', [ItemController::class, 'destroy']);
    Route::middleware($all_permissions)->get('/itens/export', [ItemController::class,'exportFiles']);
    Route::middleware($all_permissions)->post('/update/competence/{id}', [ItemController::class,'updateCompetence']);

    //Operações
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operations/managers', [OperationController::class, 'index']);

    //Executivo
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo', [ExecutiveController::class, 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo/manager', [ExecutiveController::class, 'getById']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executives', [ExecutiveController::class, 'getAllExecutives']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo/allmanager', [ExecutiveController::class, 'getAllManager']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->delete('/executivo/{id}', [ExecutiveController::class, 'delete']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->post('/executivo', [ExecutiveController::class, 'create']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->post('/executivo/{id}', [ExecutiveController::class, 'update']);

    //Competencia
    Route::middleware($all_permissions)->get('/competencias', [FileCompetenceController::class, 'getAll']);

    //LOG
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->get('/log', [LogController::class, 'show']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->post('/log/contract', [LogController::class,'getLogName']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->post('/log/collaborator', [LogController::class, 'getLogCollaborator']);

    //Timelines
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->get('/timelines/contract/{id}', [TimelineController::class,'contract']);


    //Automação
    Route::middleware('check.permission: Admin')->post('/automacao', [FileController::class, 'automacao']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->delete('/file/{file_id}/{item_id}', [FileController::class, 'deleteFile']);

    //notifications
    Route::middleware('check.permission: Admin')->get('/notifications', [NotificationController::class, 'notifications']);
    Route::middleware('check.permission: Admin')->post('/notifications/viewer', [NotificationController::class, 'notificationsViewer']);

   //OperationManager
   Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operation/executives', [OperationManagerController::class, 'getAllExecutives']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operation/allmanager', [OperationManagerController::class, 'getAllManager']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->post('/operation/manager', [OperationManagerController::class, 'create']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->post('/operation/manager/{id}', [OperationManagerController::class, 'update']);

    //Arquivos
    Route::post('/files/importRH', [FileController::class, 'importRH']);
    Route::middleware($all_permissions)->post('/files/checklist/', [FileController::class, 'addChecklistFiles']);
    
});
Route::get('/users/birthdays', [UserController::class , 'birthdays']);
Route::middleware('sys.auth')->get('/teste', [TesteController::class, 'novoteste2']);
Route::post('/teste', [TesteController::class, 'novoteste']);
Route::get('/teste2', [TesteController::class, 'novoteste2']);

Route::get('/documentation', function () {
    return view('documentation');
});

Route::fallback(function () {
    return response()->json(['error' => 'Recurso não encontrado!'], 404);
});
