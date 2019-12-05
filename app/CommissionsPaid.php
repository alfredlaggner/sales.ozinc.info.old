<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommissionsPaid extends Model
{
    protected $fillable = ['saved_commissions_id','ext_id','is_paid','paid_at','paid_by'];

    public function salesline(){

        return $this->hasOne('App\Salesline', 'ext_id', 'ext_id');
    }
}
