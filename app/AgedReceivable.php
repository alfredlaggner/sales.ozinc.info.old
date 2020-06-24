<?php

namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\Searchable;

    class AgedReceivable extends Model
    {
        use Searchable;
        public $asYouType = true;

        public function toSearchableArray()
        {
            $array = $this->toArray();

            return $array;
        }

        protected $fillable = ['invoice_id', 'customer_id', 'amount_to_collect', 'saved_residual'];

        public function receivableTotals()
        {
            return $this->hasOne('App\AgedReceivableTotal', 'customer_id', 'customer_id');
        }
    }
