<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Scout\Searchable;

class SaleInvoice extends Model
{
    //  use Searchable;

    protected $table = 'saleinvoices';

    protected $fillable = ['commission', 'comm_percent', 'comm_version', 'comm_region', 'updated_at', 'created_at'];

    protected static function boot()
    {
        parent::boot();
        /*
             static::addGlobalScope('age', function (Builder $builder) {
                    $builder->where('saleinvoices.sales_person_id', '>', 0)
                        ->where('saleinvoices.margin', '>', -100)
                        ->where('saleinvoices.margin', '<', 100)
                   //     ->where('saleinvoices.amt_to_invoice', '>=', 0)
                        ->where(function ($query) {
                            $query->where('saleinvoices.invoice_status', '=', 'invoiced')
                                ->orWhere('saleinvoices.is_paid', '=', true);
                        });
                });*/
        static::addGlobalScope('age', function (Builder $builder) {
			$builder->where('saleinvoices.sales_person_id', '>', 0)
				->where('saleinvoices.margin', '>', -100)
				->where('saleinvoices.margin', '<', 100)
				->whereYear('saleinvoices.invoice_date', '=', 2019)
                ->where(function ($query) {
                    $query->where('saleinvoices.invoice_status', '=', 'invoiced')
                        ->orWhere('saleinvoices.invoice_status', '=', 'to invoice');
                });
        });
    }

    public function salesperson()
    {
        return $this->hasOne('App\SalesPerson', 'sales_person_id', 'sales_person_id');
    }

    public function customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id', 'ext_id');
    }

    //  public $asYouType = true;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    /*    public function toSearchableArray()
        {
            $array = $this->toArray();

            // Customize array...

            return $array;
        }*/
}
