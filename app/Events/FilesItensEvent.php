<?php

namespace App\Events;

use App\Models\FilesItens;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FilesItensEvent
{
    use Dispatchable, SerializesModels;

    public $filesItens;

    public function __construct(FilesItens $filesItens)
    {
        $this->filesItens = $filesItens;

        // signed_by now call user_id
        // Update the related Item's status
        $item = $this->filesItens->item;
        $item->status = $item->file_itens->isNotEmpty(); // Set status to true if file_itens is not empty
        $item->save();

        // Update the related Checklist's completion
        $checklist = $item->checklist;
        $completedItemsCount = $checklist->itens()->where('status', true)->count();
        $totalItemsCount = $checklist->itens()->count();
        $checklist->completion = number_format(($totalItemsCount > 0) ? ($completedItemsCount / $totalItemsCount) * 100 : 0, 2);

        $checklist->status_id = 1;

        if ($checklist->completion > 0 && $completedItemsCount > 0) {
            $checklist->status_id = 2;
        }
        
        if (floatval($checklist->completion) >= 100) {
            if ($checklist->accepted_by === null) {
                $checklist->status_id = ($checklist->signed_by === null) ? 3 : 4;
            } elseif ($checklist->signed_by !== null) {
                $checklist->status_id = 5;
            }
        }
        


        $checklist->save();
    }
}
