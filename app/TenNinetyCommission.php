<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TenNinetyCommission extends Model
{
    protected $table = 'ten_ninety_commssions';
    protected $fillable = ['rep_id', 'month', 'year', 'volume_collected', 'goal', 'is_ten_ninety'];

    public function rep()
    {
        return $this->hasOne('App\SalesPerson', 'sales_person_id', 'rep_id');
    }
}
