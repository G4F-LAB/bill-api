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
            $checkChecklistExpired = new ChecklistController(new Checklist,new Collaborator);
            $checkChecklistExpired->checkChecklistExpired();

        }catch(\Exception $e){
            return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
        }
    }
}





