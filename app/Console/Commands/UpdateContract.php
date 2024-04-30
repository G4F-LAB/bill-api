<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ContractController;
use App\Models\Collaborator;

class UpdateContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatecontract:send';

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
            $contract= new ContractController(new Collaborator);
            $contract->updateContracts();
           
        }catch(\Exception $e){
            return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
        }
    }
}





