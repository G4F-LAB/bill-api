<?php

namespace App\Events;

use App\Models\FilesItens;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Services\ChecklistUpdateService;

class FilesItensEvent
{
    use Dispatchable, SerializesModels;

    public $filesItens;

    public function __construct(FilesItens $filesItens)
    {
        $this->filesItens = $filesItens;

        // Update the related Item's status
        $item = $this->filesItens->item;
        $item->status = $item->file_itens->isNotEmpty(); // Set status to true if file_itens is not empty
        $item->save();

        // Update the related Checklist's completion

       $checklistUpdateEvent = new ChecklistUpdateService($item->checklist);

    }

 
}
