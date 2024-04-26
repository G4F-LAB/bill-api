<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Checklist extends Model

{
    use LogsActivity;
    protected $connection =  'book';
    protected $primaryKey = 'id';
    protected $fillable = [
        'contract_id',
        'date_checklist',
        'object_contract',
        'shipping_method',
        'obs',
        'accept',
        'user_id'
    ];

    public function rules(){
        return [
        'contract_id' => 'required|int',
        'date_checklist' => 'required|date',
        'object_contract' => 'required|string',
        'shipping_method' => 'required|string',
        'obs' => 'string',
        'accept' => 'boolean',
        'sector' => 'required|string',
        'user_id' => 'string'

        ];
    }


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->useLogName('Checklist')->logOnly([
        'contract_id',
        'date_checklist',
        'object_contract',
        'shipping_method',
        'obs',
        'accept',
        'sector',
        'user_id']);
    }

    public function feedback() {
        return[
            'contract.required' => 'O campo do contrato é de preenchimento obrigatório.',
            'date_checklist.required' => 'O campo do data é de preenchimento obrigatório.',
            'shipping_method.required' => 'O campo do forma de envio é de preenchimento obrigatório.',
            'sector.required' => 'O campo do setor é de preenchimento obrigatório.'

        ];
    }

    public function contract(){
        return $this->belongsTo(Contract::class, 'contract_uuid', 'id');
    }

    public function itens() {
        return $this->hasMany(Item::class);
    }

    public function status() {
        return $this->belongsTo(StatusChecklist::class);
    }

    public function getManager() {
        return $this->belongsTo(StatusChecklist::class);
    }

    public function sync_itens($id = NULL) {
        if($id == NULL) $id = $this->id;
        $checklist = $this->with('itens')
            ->whereHas('itens', function($query) {
                $query->where('mandatory', true);
            })->find($id);
        $total_itens = count($checklist->itens);
        $total_complete = 0;

        $progress = false;

        if($total_itens > 0) {
            foreach($checklist->itens as $index => $item){
                if($item->status){
                    $total_complete = $total_complete + 1;
                    $progress = true;
                }
            }

            $percentage = floor(($total_complete*100)/$total_itens);
            if($checklist->completion == 0 && $progress) $checklist->status_id = 2;
            if($checklist->completion < 100 && $percentage == 100) $checklist->status_id = 3;
            $checklist->completion = $percentage;
            $checklist->save();
        }
        return $checklist;
    }

    public function update_status() {
        $ok = false;
        if(!empty($this->user_id) && $this->completion == 100){
            $this->status_id = 4;
            $ok = true;
        }
        if(!empty($this->user_id) && $accept && $this->completion == 100){
            $this->status_id = 5;
            $ok = true;
        }
        return $ok;
    }
}
