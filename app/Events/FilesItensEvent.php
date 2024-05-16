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

        if ($checklist->completion === 100 && $checklist->signed_by === null ) {
            $checklist->status_id = 3;
        }

        if ($checklist->completion === 100 && isset($checklist->signed_by) && $checklist->user_id === null ) {
            $checklist->status_id = 4;
        }

        if ($checklist->completion === 100 && isset($checklist->signed_by) && isset($checklist->user_id) ) {
            $checklist->status_id = 4;
        }


        $checklist->save();
    }
}
