<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceNote extends Model
{
    protected $fillable = ['invoice_id','customer_id','user_id','customer_name','note','note_by'];

    public function customer(){

		return $this->belongsTo('App\Customer', 'customer_id', 'ext_id');
	}

}
