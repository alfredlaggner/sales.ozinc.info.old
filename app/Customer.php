<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Customer extends Model
{
  //  use Searchable;
    public $asYouType = true;

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

    public function sales_lines()
    {
        return $this->hasMany('App\SaleInvoice',  'customer_id','ext_id');
    }


}
