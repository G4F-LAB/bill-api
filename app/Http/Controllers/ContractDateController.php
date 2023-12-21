<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContractDateController extends Controller
{
    public function __construct(Contract $contract, Checklist $checklist)
    {

        // $this->contract = $contract;
        $this->checklist = $checklist;
    }

    public function getListChecklist(Request $request)
    {
        try {
            $data = $request->data;
            $contractId = $request->id;

            $filterChecklist = $this->checklist->where('contract_id', $contractId)
                ->whereRaw("EXTRACT(YEAR FROM date_checklist) || '-' || LPAD(EXTRACT(MONTH FROM date_checklist)::text, 2, '0') = ?",[$data])->get();
                return response()->json($filterChecklist);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }
}
