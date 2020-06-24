<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class SalesPerson extends Model
{
    use HasRoles;
    use SoftDeletes;

    protected $table = 'sales_persons';

    /*    protected static function boot()
        {
            parent::boot();

            static::addGlobalScope('age', function (Builder $builder) {
                $builder->where('is_salesperson', '=', 1);
            });
        }*/
    public function salesperson()
    {
        return $this->hasOne('App\SaleInvoice', 'sales_person_id', 'sales_person_id');
    }
}
