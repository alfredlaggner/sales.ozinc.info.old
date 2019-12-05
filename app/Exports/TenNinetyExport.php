<?php


namespace App\Exports;

use App\AgedReceivable;
use App\TenNinetyCommission;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;

class TenNinetyExport implements FromView
{
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $ars = TenNinetyCommission::where('month', $this->data)
            ->where('goal', '>', 0)
            ->where('volume', '>', 0)
            ->orderby('is_ten_ninety','desc')
            ->get();
        return view('exports.ten_ninety_commissions', ['ars' => $ars]);
    }
}
