<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ChecklistController;
use App\Models\Checklist;
use App\Models\Collaborator;
use App\Models\Contract;
use App\Notifications\ChecklistNotification;
use Notification;
use App\Mail\Checklist\Created as ChecklistCreated;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Notifications\CheckChecklistExpired;

class SendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendemail:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    function handle()
    {
        try{
            $dataAtual = now();
            $dataComparacao = $dataAtual->subDays(3);
            $checklistExpired = Checklist::whereDate('date_checklist', '>=', $dataComparacao)->get()->toArray();

            // $checklistExpired = Checklist::whereDate('date_checklist', '>=', $dataComparacao)->get()->pluck('contract_id')->toArray();
            // //$collaborator = Operation::with('collaborator')->where('id',)->first()->toArray();
            // foreach($checklistExpired as $key => $contract_id)
            // {
            //     $collaborator = Contract::with('operation.collaborators')->where('id',$contract_id)->first();
            //    // dd($collaborator);
            // }
            // var_dump($collaborator);
            //$to_collaborators = Collaborator::whereIn('permission_id', PERMISSIONS_RH_FIN)->get()->pluck('email');
            // Mail::to("talis.santiago@g4f.com.br")->send(new ChecklistCreated(593));
            // $email = ["alertas.ti@g4f.com.br"];
            // Mail::send('welcome',[], function ($message)use($email){
            //     $message->to('talis.santiago@g4f.com.br')
            //             ->subject('Assunto do e-mail');
            // });
            Notification::sendNow( [], new ChecklistNotification($checklistExpired, "talis.santiago@g4f.com.br"));
            return response()->json("Email enviado com sucesso", 200);
            $this->info('E-mail enviado com sucesso ...');
        }catch(\Exception $e){
            return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
        }
          
        
    }
}





