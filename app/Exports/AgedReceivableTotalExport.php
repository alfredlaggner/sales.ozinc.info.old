<?php

namespace App\Exports;

use App\AgedReceivablesTotal;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class AgedReceivableTotalExport implements FromView
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return Collection
     */
    public function view(): View
    {
        if ($this->data) {
            $ar_totals = AgedReceivablesTotal::where('rep_id', $this->data)->orderby('rep')->orderby('customer')->get();
        } else {
            $ar_totals = AgedReceivablesTotal::orderby('rep')->orderby('customer')->get();
        }

        return view('exports.ar_totals', ['ars_totals' => $ar_totals]);
    }
}
