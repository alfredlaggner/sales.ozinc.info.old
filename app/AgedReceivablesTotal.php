<?php

namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Laravel\Scout\Searchable;

    class AgedReceivablesTotal extends Model
    {
        use Searchable;
        public $asYouType = true;

        protected $fillable = ['is_felon'];

        public function toSearchableArray()
        {
            $array = $this->toArray();

            return $array;
        }

        public function receivables()
        {
            return $this->hasMany('App\AgedReceivable', 'customer_id', 'customer_id');
        }
    }
