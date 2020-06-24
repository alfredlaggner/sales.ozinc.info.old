<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Scout\Searchable;

class Customer extends Model
{
    use Notifiable;
    use Searchable;
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
        return $this->hasMany(\App\SaleInvoice::class, 'customer_id', 'ext_id');
    }

    public function notes()
    {
        return $this->hasMany(\App\InvoiceNote::class, 'ext_id', 'customer_id');
    }
}
