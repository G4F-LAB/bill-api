<?php

namespace App\Services;

use App\Models\Checklist;

class ChecklistUpdateService
{
    public $checklist;

    public function __construct(Checklist $checklist)
    {
        $this->checklist = $checklist;

        // Update the completion and status of the checklist
        $this->updateChecklist($this->checklist);

        //notification?

       
    }

    protected function updateChecklist($checklist)
    {
        $completedItemsCount = $checklist->itens()->where('status', true)->count();
        $totalItemsCount = $checklist->itens()->count();
        $checklist->completion = number_format(($totalItemsCount > 0) ? ($completedItemsCount / $totalItemsCount) * 100 : 0, 2);

        $checklist->status_id = 1;

        if ($checklist->completion > 0 && $completedItemsCount > 0) {
            $checklist->status_id = 2;
            $checklist->signed_by = null;
            $checklist->accepted_by = null;
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
