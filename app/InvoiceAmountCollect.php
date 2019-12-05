<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceAmountCollect extends Model
{
	protected $table = 'invoice_amt_collects';

    protected $fillable = ['invoice_id','customer_id','user_id','customer_name','amount_to_collect','saved_residual'];

}
