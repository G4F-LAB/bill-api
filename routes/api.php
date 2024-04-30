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
Route::middleware(['sec.check', 'handle.cors', 'sys.auth'])->group(function () {

    //Auth
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::get('/refresh', [AuthController::class, 'refresh']);
    Route::put('/update_info', [AuthController::class, 'update_info']);

    //Setup
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,TI,Geral')->get('/setup/navigation', [SetupController::class, 'navigation']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,TI,Geral')->post('/setup/navigation', [SetupController::class, 'navigation_upsert']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,TI,Geral')->delete('/setup/navigation/{id}', [SetupController::class, 'navigation_delete']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,TI,Geral')->get('/setup/permissions', [SetupController::class, 'permissions']);


    //Colaboradores e Permissões
    Route::middleware('check.permission: Admin, Executivo, Operação, TI')->get('/colaboradores', [CollaboratorController::class , 'getAllDb']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI')->get('/colaboradores/manager', [CollaboratorController::class , 'getAllManagers']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI')->put('/collaborators', [CollaboratorController::class , 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI')->get('/collaborators/permissions', [CollaboratorController::class , 'collaboratorsByPermission']);
    Route::middleware('check.permission: Admin, Executivo, TI')->post('/collaborators/create', [CollaboratorController::class , 'create']);

    //Users
    Route::middleware('check.permission: Admin, Executivo, Operação, TI')->get('/users', [UserController::class , 'index']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI')->get('/users/types', [UserController::class , 'getUsersGroupedByType']);
    Route::middleware('check.permission: Admin, Executivo, Operação, TI')->put('/users', [UserController::class , 'update']);

    //Vincular/desvincular um colaborador a uma operação
    Route::middleware('check.permission: Admin, Executivo, Operação')->post('/collaborator/operation', [CollaboratorController::class , 'collaboratorOperation']);
    Route::middleware('check.permission: Admin, Executivo, Operação')->post('/collaborator/oper', [CollaboratorController::class , 'unlinkCollaborator']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->get('/collaborators/typescontracts', [ContractController::class , 'getContractsOfCollab']);


    //Contratos
    Route::middleware('check.permission: Admin, Executivo, Operação, Analista')->get('/contracts', [ContractController::class, 'index']);
    Route::middleware('check.permission: Admin, Executivo, Operação, Analista')->put('/contracts', [ContractController::class, 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operação, Analista')->post('/contracts/update', [ContractController::class, 'update']);
    Route::middleware('check.permission: Admin, Executivo, Operação')->post('/contracts/new', [ContractController::class, 'updateListContracts']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->get('/contracts/{id}/checklist', [ContractController::class, 'checklistByContractID']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->post('/default/files', [FileController::class,'uploadDefaultFiles']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->post('/contract/ocurrences', [FileController::class,'searchOcurrence']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->post('/contract/files', [FileController::class,'uploadContractFiles']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->post('/contract/create', [ContractController::class,'createContract']);

    //CheckList//
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista')->get('/checklists/{id}', [ChecklistController::class,'get']);

    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,TI,Geral')->get('/checklist/updateContractId', [ChecklistController::class , 'updateContactIds']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,TI,Geral')->get('/checklist', [ChecklistController::class , 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista')->post('/checklist',[ChecklistController::class, 'store']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista')->post('/checklist/{id}', [ChecklistController::class,'update']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista')->patch('/checklist/{id}', [ChecklistController::class,'update']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista')->post('/checklist/{id}/files', [FileController::class,'uploadChecklistFiles']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->get('/checklist/{id}/items', [ChecklistController::class,'checklistItens']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista')->get('/checklist/{id}/items/create', [ChecklistController::class,'checklistItensCreate']);
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista')->get('/checklist/{id}/filter', [ContractDateController::class,'getListChecklist']);
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista')->get('/checklist/{id}/{reference}', [ChecklistController::class,'getDataChecklist']);
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista')->get('/competence', [ChecklistController::class,'getAllCompetence']);
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista')->get('/check/checklist', [ChecklistController::class,'checkChecklistExpired']);
    //automate
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista')->get('/automate/checklist/items/duplicateall', [ChecklistController::class,'duplicateall']);

    //Analytics
    Route::prefix('/analytics')->group(function () {
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operations',[AnalyticsController::class,'getOperationsByUser']);
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/contracts/{id}',[AnalyticsController::class,'getContractsByOperation']);
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/checklistcomplete',[AnalyticsController::class,'check_complete']);
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/checklistsstatus',[AnalyticsController::class,'qtdStatusChecklists']);
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/contracts',[AnalyticsController::class,'contractsAll']);
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/collaborators/{id}', [AnalyticsController::class,'getCollaboratorById']);
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/collaborators', [AnalyticsController::class,'getAllCollaborators']);
        Route::middleware('check.permission: Admin,Executivo,Operação')->get('/checklist', [AnalyticsController::class,'getChecklist']);
        // Route::middleware('check.permission: Admin,Executivo,Operação')->get('/analytics',[AnalyticsController::class,'getMyAnalytics']);
    });

    Route::middleware('check.permission: Admin')->get('/directory',[DirectoryController::class,'getAnalyticsDirectory']);
    // Route::middleware('check.permission: Admin,Executivo,Operação')->get('/analytics/{id}',[AnalyticsController::class,'getMyAnalytics']);

    //Nomenclaturas padrão dos arquivos
    
    Route::middleware('check.permission:Admin')->prefix('filenames')->group(function () {
        Route::get('/', [FileNameController::class, 'index']);
        Route::post('/', [FileNameController::class, 'create']);
        Route::get('/{id}', [FileNameController::class, 'get']);
        Route::put('/{id}', [FileNameController::class, 'update']);
        Route::delete('/{id}', [FileNameController::class, 'delete']);
      
    });
    
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->get('/filenaming', [FileNamingController::class, 'getAll']);    
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->get('/filenaming/{id}', [FileNamingController::class, 'getByID']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->post('/filenaming', [FileNamingController::class, 'store']);
    Route::middleware('check.permission: Admin')->put('/filenaming/{id}', [FileNamingController::class, 'update']);
    Route::middleware('check.permission: Admin')->delete('/filenaming', [FileNamingController::class, 'delete']);
    Route::middleware('check.permission: Admin')->get('/filenaming/checklist/{id}', [FileNamingController::class, 'getAllRelCheklist']);
    Route::middleware('check.permission: Admin')->get('/filetypes', [FileNamingController::class, 'getFileCatogary']);

    //Itens
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->get('/item', [ItemController::class, 'show']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->get('/item/{id}', [ItemController::class, 'getbyID']);
    Route::middleware('check.permission: Admin')->post('/item', [ItemController::class, 'store']);
    Route::middleware('check.permission: Admin')->put('/item/{id}', [ItemController::class, 'update']);
    Route::middleware('check.permission: Admin')->put('/item/competence/{id}', [ItemController::class, 'updateCompetence']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->delete('/item/{id}', [ItemController::class, 'destroy']);
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista,Processos')->get('/itens/export', [ItemController::class,'exportFiles']);
    Route::middleware('check.permission:Admin,Executivo,Operação,Analista')->post('/update/competence/{id}', [ItemController::class,'updateCompetence']);

    //Operações
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operacoes', [OperationController::class, 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->delete('/operacoes/{id}', [OperationController::class, 'delete']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->post('/operacoes', [OperationController::class, 'create']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->post('/operacoes/{id}', [OperationController::class, 'update']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operations', [OperationController::class, 'getAllOperations']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operations/managers', [OperationController::class, 'getAllManagerofOperation']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->post('/collab_operacoes', [OperationController::class, 'deleteCollabOperation']);

    //Executivo
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo', [ExecutiveController::class, 'getAll']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo/manager', [ExecutiveController::class, 'getById']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executives', [ExecutiveController::class, 'getAllExecutives']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo/allmanager', [ExecutiveController::class, 'getAllManager']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->delete('/executivo/{id}', [ExecutiveController::class, 'delete']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->post('/executivo', [ExecutiveController::class, 'create']);
    Route::middleware('check.permission: Admin,Executivo,Operação')->post('/executivo/{id}', [ExecutiveController::class, 'update']);

    //Competencia
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro')->get('/competencias', [FileCompetenceController::class, 'getAll']);

    //LOG
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->get('/log', [LogController::class, 'show']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->post('/log/contract', [LogController::class,'getLogName']);
    Route::middleware('check.permission: Admin,Executivo,Operação,Analista,RH,Financeiro,Geral')->post('/log/collaborator', [LogController::class, 'getLogCollaborator']);

    //Automação
    Route::middleware('check.permission: Admin')->post('/automacao', [FileController::class, 'automacao']);
    Route::middleware('check.permission:All')->delete('/file/{file_id}/{item_id}', [FileController::class, 'deleteFile']);

    //notifications
    Route::middleware('check.permission: Admin')->get('/notifications', [NotificationController::class, 'notifications']);
    Route::middleware('check.permission: Admin')->post('/notifications/viewer', [NotificationController::class, 'notificationsViewer']);

//    //OperationManager
//    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo', [ExecutiveController::class, 'getAll']);
//    Route::middleware('check.permission: Admin,Executivo,Operação')->get('/executivo/manager', [ExecutiveController::class, 'getById']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operation/executives', [OperationManagerController::class, 'getAllExecutives']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->get('/operation/allmanager', [OperationManagerController::class, 'getAllManager']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->delete('/operation/delete{id}', [OperationManagerController::class, 'delete']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->post('/operation/manager', [OperationManagerController::class, 'create']);
   Route::middleware('check.permission: Admin,Executivo,Operação')->post('/executivo/{id}', [ExecutiveController::class, 'update']);

    //Arquivos
    Route::post('/files/importRH', [FileController::class, 'importRH']);
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
