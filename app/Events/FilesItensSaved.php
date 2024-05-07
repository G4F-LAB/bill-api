<?php

namespace App\Events;

use App\Models\FilesItens;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FilesItensSaved
{
    use Dispatchable, SerializesModels;

    public $filesItens;

    public function __construct(FilesItens $filesItens)
    {
        $this->filesItens = $filesItens;

        // Update the related Item's status
        $item = $this->filesItens->item;
        $item->status = true;
        $item->save();

        // Update the related Checklist's completion
        $checklist = $item->checklist;
        $completedItemsCount = $checklist->itens()->where('status', true)->count();
        $totalItemsCount = $checklist->itens()->count();
        $checklist->completion = number_format(($totalItemsCount > 0) ? ($completedItemsCount / $totalItemsCount) * 100 : 0, 2);

        if ($checklist->completion == 0 && $completedItemsCount > 0) {
            $checklist->status_id = 2;
        }

        // if ($checklist->completion < 100 && $percentage == 100) {
        //     $checklist->status_id = 3;
        // }

        
        $checklist->save();
    }
}
