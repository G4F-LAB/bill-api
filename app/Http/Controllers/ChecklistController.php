<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\FileNaming;
use App\Models\FileName;
use App\Models\Contract;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ItemController;
use App\Notifications\ChecklistNotification;
use App\Notifications\ChecklistExpired;

use Notification;
use App\Models\FileCompetence;
use App\Models\Notification as ModelsNotification;
use App\Models\Operation;
use App\Models\User;
use DateTime;

const PERMISSIONS_RH_FIN = ['RH', 'Financeiro'];

class ChecklistController extends Controller
{

    public $checklist;
    public function __construct(Checklist $checklist, User $user)
    {
        $this->user = $user->getAuthUser();
        $this->checklist = $checklist;
    }


    public function show($id)
    {
        // Retrieve the checklist along with its items
        $checklist = Checklist::with(['contract', 'itens.file_name.task.integration', 'itens.files'])->find($id);

        if (!$checklist) {
            return response()->json(['error' => 'Checklist not found'], 404);
        }

        return response()->json($checklist, 200);
    }


    public function updateContactIds()
    {
        $contract_ids = Contract::on('book')->get();

        $contract_uiids = Contract::on('data_G4F')->get();

        $checklists = Checklist::all();

        foreach ($checklists as $checklist) {
            $contractId = $contract_ids->firstWhere('id', $checklist->contract_id);

            $contractUuid = $contract_uiids->firstWhere('name', $contractId->name);


            if (isset($contractUuid->id)) {
                $checklist->contract_uuid = $contractUuid->id;
            }


            $checklist->save();
        }

        return response()->json($checklists, 200);
    }


    public function getAll()
    {
        try {
            $checklists = Checklist::whereNotNull('contract_uuid')
                ->with(['contract' => function ($query) {
                    $query->where('status', 'Ativo');
                }, 'contract.operation'])
                ->get();

            return response()->json($checklists, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Não foi possivel acessar a checklist'], 500);
        }
    }


    public function getAllCompetence()
    {
        try {
            $competence = FileCompetence::All();
            return response()->json($competence, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Não foi possivel acessar as Competências'], 500);
        }
    }

    public function store(Request $request)
    {
        // return $request->contract_uuid;
        try {
            $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            $notification = new NotificationController($user);
            $data_notification = new ModelsNotification();
            $checklistExists = Checklist::where('contract_uuid', $request->contract_uuid)
                ->whereYear('date_checklist', '=', date('Y', strtotime($request->date_checklist)))
                ->whereMonth('date_checklist', '=', date('m', strtotime($request->date_checklist)))
                ->exists();

            // return ;
            if ($checklistExists) {
                return response()->json(['error' => 'Não foi possivelxx criar checklist, já existe esse checklist.'], 404);
            };


            $this->checklist->contract_uuid  = $request->contract_uuid;
            $this->checklist->date_checklist  = $request->date_checklist;
            // $this->checklist->month_reference = date('m', strtotime($request->date_checklist));
            $this->checklist->object_contract = $request->object_contract;
            $this->checklist->shipping_method = $request->shipping_method;
            $this->checklist->obs = $request->obs;
            $this->checklist->accept = $request->accept;
            $this->checklist->user_id = $request->signed_by;

            $this->checklist->save();

            //Notification
            $data_notification->desc_id = 2;
            $data_notification->notification_cat_id = 2;
            $data_notification->contract_uuid = $this->checklist->contract_uuid;
            $data_notification->notification_type_id = 1;
            $notification->registerNotification($data_notification);

            if ($request->duplicate != null) {
                $duplicated = $this->duplicateItems($request->duplicate, $this->checklist->id, $request->contract_uuid);
                return 'teste duoplicate';
                if (isset($duplicated['error'])) {
                    return response()->json(['message' => $duplicated], 200);
                }
            }

            // Send Notifications
            $to_users = User::whereIn('type', PERMISSIONS_RH_FIN)->get()->pluck('email');
            // Notification::sendNow( [], new ChecklistNotification($this->checklist, $to_users));

            return response()->json(['message' => 'Checklist criado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function duplicateItems($duplicate, $checklist, $contract_uuid)
    {

        $data = [
            "file_naming_id" => [],
            "checklist_id" => $checklist,
            "status" => false,
            "file_competence_id" => 1
        ];

        $checklist_id_copy = Checklist::where([
            'contract_uuid' => $contract_uuid,
            'date_checklist' => $duplicate . '-01'
        ])->value('id');

        $data['file_naming_id'] = Item::where('checklist_id', $checklist_id_copy)->pluck('file_naming_id');

        $itemController = new ItemController(null);

        return $itemController->addItems($data);
    }


    public function update(Request $request, $id)
    {
        try {
            $user = User::where('taxvat', Auth::user()['employeeid'])->first();
            $notification = new NotificationController($user);
            $data_notification = new ModelsNotification();
            $this->checklist = $this->checklist->find($id);
            if ($request->method() == 'PATCH') {
                $dinamicRules = array();

                //aplica regras dinâmicas para os campos que foram enviados
                foreach ($this->checklist->rules() as $input => $rule) {
                    if (array_key_exists($input, $request->all())) {
                        $dinamicRules[$input] = $rule;
                    }
                }
                $request->validate($dinamicRules, $this->checklist->feedback());
            } else {
                $request->validate($this->checklist->rules(), $this->checklist->feedback());
            }

            if ($request->has('date_checklist')) $this->checklist->date_checklist  = $request->date_checklist;
            if ($request->has('object_contract')) $this->checklist->object_contract = $request->object_contract;
            if ($request->has('shipping_method')) $this->checklist->shipping_method = $request->shipping_method;
            if ($request->has('obs')) $this->checklist->obs = $request->obs;
            if ($request->has('accept')) $this->checklist->accept = $request->accept;
            if ($request->has('signed_by')) $this->checklist->user_id = $request->signed_by;
            // validação pendente
            if (!empty($this->checklist->user_id) && !$this->checklist->accept && $this->checklist->completion == 100) $this->checklist->status_id = 4;
            // finalizado
            if (!empty($this->checklist->user_id) && $this->checklist->accept && $this->checklist->completion == 100) $this->checklist->status_id = 5;

            $this->checklist->update();

            //Notification
            if ($this->checklist->status_id = 5 && $this->checklist->getChanges()) {
                $data_notification->desc_id = 4;
                $data_notification->notification_cat_id = 2;
                $data_notification->contract_id = $this->checklist->contract_uuid;
                $data_notification->notification_type_id = 1;
                $notification->registerNotification($data_notification);
            }

            if ($this->checklist->getChanges()) {
                $this->checkChecklistNotification($this->checklist->getAttributes()["id"], $this->checklist->status_id);
            }
            return response()->json(['message' => 'Checklist atualizado com sucesso'], 200);
        } catch (\Exception $e) {
            return response()->json(['erro' => $e->getMessage()], 500);
        }
    }

    function checklistItensCreate(Request $request)
    {

        try {
            //define as datas
            $dataAtual = Carbon::now();
            //$dataAtual = Carbon::createFromFormat('Y-m-d', '2024-01-31')->startOfMonth();
            $months = [];
            $id_contract = $request->id;
            $current_dates = [];

            for ($i = 2; $i >= 0; $i--) {
                $months[] = $dataAtual->copy()->subMonths($i)->format('Y-m');
            }

            $months[] = $dataAtual->copy()->addMonth(1)->format('Y-m');

            foreach ($months as $month) {

                $count_items = Item::where('checklist_id', function ($query) use ($id_contract, $month) {

                    $query->select('id')
                        ->from('checklists')
                        ->where('contract_uuid', $id_contract)
                        ->where('date_checklist', $month . '-01');
                })->count();

                // $current_dates[] = array('date' => $month, 'items' => $count_items);
                $current_dates[] = array('date' => $month, 'items' => $count_items > 0 ? true : false);
            }
            //define as datas

            // return $current_dates;

            $id = $request->id;
            $reference = ($request->reference) ? $request->reference : $months[2];

            $itens = Item::with('checklist', 'file_name', 'file_competence')->whereHas('checklist', function ($query) use ($id, $reference) {
                $query->whereRaw("TO_CHAR(checklists.date_checklist, 'YYYY-MM') LIKE '" . $reference . "%' and checklists.contract_uuid = '" . $id . "'");
            })->get();
            // return $itens;

            $id_checklist = Checklist::where('contract_uuid', $id)
                ->whereRaw("TO_CHAR( checklists.date_checklist, 'YYYY-MM' ) LIKE '" . $reference . "%'")
                ->value('id');


            $contract = Contract::where('id', $id)->first();

            $data['contract'] = $contract;
            $data['checklist_id'] = $id_checklist;
            $data['itens'] = $itens;
            $data['current_dates'] = $current_dates;

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function checklistItens(Request $request)
    {
        try {
            $type = NULL;
            if ($this->user->type  === 'RH') {
                $type = 1;
            }
            if ($this->user->type  === 'Financeiro') {
                $type = 2;
            }
            $items = Item::with('file_name')->with('files')
                ->when(!empty($type), function ($query) use ($type) {
                    $query->whereHas('file_name', function ($query2) use ($type) {
                        $query2->where('file_type_id', $type);
                    });
                })
                ->where('checklist_id', $request->id)->get();
            $this->checklist = Checklist::with('status')->find($request->id);
            $contract = Contract::where('id', $this->checklist->contract_uuid);

            $data['checklist'] = $this->checklist;
            $data['contract'] = $contract;
            $data['items_name'] = FileName::whereIn('id', $items->pluck('file_naming_id'))->pluck('standard_file_naming');

            $items = $items->toArray();
            foreach ($items as $index => $item) {
                $files = $item['files'];
                foreach ($files as $index2 => $file) {
                    if (!empty($file)) {
                        $items[$index]['files'][$index2]['full_path'] = env('AWS_URL') . $file['path'];
                    }
                }
            }

            $data['itens'] = $items;

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function getDataChecklist(Request $request)
    {
        try {

            $data = Checklist::where('contract_uuid', $request->id)
                ->select('object_contract', 'obs', 'shipping_method')
                ->where('date_checklist', $request->reference . '-01')
                ->first();

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    function duplicateall(Request $request)
    {

        try {
            $date = Carbon::now();

            $ids_contracts = Contract::where('contractual_situation', true)->pluck('id');
            $date_atual = $date->format('Y-m');
            $date_reference = $date->subMonth()->format('Y-m');


            foreach ($ids_contracts as $id_contract) {
                $checklist = new Checklist();
                $id_checklist = null;

                $id_checklist_reference = Checklist::where('contract_id', $id_contract)
                    ->where('date_checklist', 'LIKE', $date_reference . '%')
                    ->first();


                if (!empty($id_checklist_reference)) {

                    $ids_items_duplicate = Item::where('checklist_id', $id_checklist_reference->id)
                        ->where('mandatory', true)
                        ->get(['file_competence_id', 'file_naming_id']);

                    $checklist_exists_atual = Checklist::where('contract_id', $id_contract)
                        ->whereYear('date_checklist', '=', date('Y', strtotime($date_atual . '-01')))
                        ->whereMonth('date_checklist', '=', date('m', strtotime($date_atual . '-01')))
                        ->first();

                    if ($checklist_exists_atual === null) {
                        $checklist->contract_id  = $id_checklist_reference->contract_id;
                        $checklist->date_checklist  = $date_atual . '-01';
                        $checklist->object_contract = $id_checklist_reference->object_contract;
                        $checklist->shipping_method = $id_checklist_reference->shipping_method;
                        $checklist->sector_id = $id_checklist_reference->sector_id;
                        $checklist->obs = $id_checklist_reference->obs;
                        $checklist->accept = $id_checklist_reference->accept;
                        $checklist->signed_by = $id_checklist_reference->signed_by;
                        $checklist->save();
                        $id_checklist = $checklist->id;
                    } else {
                        $id_checklist = $checklist_exists_atual->id;
                    }

                    if (!empty($ids_items_duplicate)) {
                        foreach ($ids_items_duplicate as $value) {
                            // print($value);exit;
                            $data = [
                                "file_naming_id" => [$value->file_naming_id],
                                "checklist_id" => $id_checklist,
                                "status" => false,
                                "file_competence_id" => $value->file_competence_id
                            ];

                            $itemController = new ItemController(null);

                            $itemController->addItems($data);
                        }
                    }
                }
            }
            return response()->json(['status' => 'ok'], 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    //rodar no servidor de produção composer require spaanproductions/laravel-carbon-holidays
    function setimoDiaUtilDoMes($ano, $mes)
    {
        $date = Carbon::createFromDate($ano, $mes, 1); // Primeiro dia do mês
        $diasUteis = 0;
        while ($diasUteis < 7) {
            // Verifica se é dia útil (segunda a sexta-feira, excluindo feriados)
            if ($date->isWeekday()
                // && !$date->isHoliday()
            ) {
                $diasUteis++;
            }
            $date->addDay(); // Avança para o próximo dia
        }

        return $date->subDay(); // Retorna o sétimo dia útil do mês
    }

    public function checkChecklistExpired()
    {
        try {
            $date = Carbon::now();
            $month = $date->month;
            $year = $date->year;
            $dataSetimoDiaUtil = $this->setimoDiaUtilDoMes($year, $month);

            $checklists = Checklist::with([
                'contract.operationContractUsers.user'
            ])->whereDate('date_checklist', '>=', $dataSetimoDiaUtil)
                // ->where('id',$id)
                ->where('status_id', '!=', 5)
                ->get()->toArray();
            echo "<pre>";
            foreach ($checklists as $key => $checklist)
                print_r($checklist); {
                if ($checklist['contract']) {
                    $contract = $checklist['contract'];
                    $emailCollab = $contract['operation_contract_users'];
                }
                // echo "<pre>";
                // Notification::sendNow( [], new ChecklistExpired($checklist, $emailCollab['email']));
                Notification::sendNow([], new ChecklistExpired($checklist, "talis.santiago@g4f.com.br"));
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function checkChecklistNotification($id = null, $status_id = null)
    {
        try {
            $status = "";
            if ($status_id == 5) {
                $status = "Finalizado";
            } else {
                $status = "Alterado";
            }
            var_dump("teste");
            exit;
            $checklists = Checklist::with([
                'contract.operationContractUsers.user'
            ])
                ->with('status_checklist')
                ->where('id', $id)
                ->get()->toArray();
            echo "<pre>";
            print_r($checklists);
            exit;
            foreach ($checklists as $key => $checklist) {
                if ($checklist['contract']) {
                    $contract = $checklist['contract'];
                    $emailCollab = $contract['operation_contract_users'];
                }
                // echo "<pre>";
                // Notification::sendNow( [], new ChecklistExpired($checklist, $emailCollab['email']));
                Notification::sendNow([], new ChecklistExpired($checklist, "talis.santiago@g4f.com.br"));
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
