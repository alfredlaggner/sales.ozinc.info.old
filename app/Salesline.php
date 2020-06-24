<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salesline extends Model
{
    public function invoice_paid()
    {
        return $this->hasOne('App\CommissionsPaid', 'ext_id', 'ext_id');
    }

    public function getCommissionsUnpaidAttribute()
    {
        return $this->invoice_paid()->where('invoice_paid.is_paid', 0)->get();
    }
}
