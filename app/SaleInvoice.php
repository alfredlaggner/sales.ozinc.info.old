<?php

	namespace App;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Database\Eloquent\Builder;
	use Laravel\Scout\Searchable;

	class SaleInvoice extends Model
	{
		//  use Searchable;

		protected $table = 'invoice_lines';

		protected $fillable = ['commission', 'comm_percent', 'comm_version', 'comm_region', 'updated_at', 'created_at'];

		protected static function boot()
		{
			parent::boot();
			/*
				 static::addGlobalScope('age', function (Builder $builder) {
						$builder->where('invoice_lines.sales_person_id', '>', 0)
							->where('invoice_lines.margin', '>', -100)
							->where('invoice_lines.margin', '<', 100)
					   //     ->where('invoice_lines.amt_to_invoice', '>=', 0)
							->where(function ($query) {
								$query->where('invoice_lines.invoice_status', '=', 'invoiced')
									->orWhere('invoice_lines.is_paid', '=', true);
							});
					});*/
			$invoice_date =
			static::addGlobalScope('age', function (Builder $builder) {
				$builder->where('invoice_lines.sales_person_id', '>', 0)
					->where('invoice_lines.margin', '>', -100)
					->where('invoice_lines.margin', '<', 100)
					->whereYear('invoice_lines.invoice_date', '=', 2019);
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

        public function salesline(){

            return $this->hasOne('App\Salesline', 'ext_id', 'ext_id');
        }

        public function invoice_paid(){

            return $this->hasOne('App\CommissionsPaid', 'ext_id', 'ext_id');
        }
        public function getCommissionsUnpaidAttribute()
        {
            return $this->invoice_paid()->where('invoice_paid.is_paid', 0)->get();

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
