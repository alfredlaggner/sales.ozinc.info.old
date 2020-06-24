<?php

namespace App\Imports;

    use App\InvoiceNote;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Collection;
    use Maatwebsite\Excel\Concerns\ToCollection;

    class ReceivableCommentsImport implements ToCollection
    {
        /**
         * @param array $row
         *
         * @return Model|null
         */
        public function collection(Collection $rows)
        {

        //	dd($rows->toArray());

            foreach ($rows as $row) {
                InvoiceNote::updateOrCreate(
                    ['sales_order' => $row[0]],
                    ['note' => $row[1],
                    'amount_to_collect' => $row[2],
                ]);
            }
        }

        /*		public function model(array $row)
                {
            //	dd($row);
                    return new InvoiceNote([
                        'sales_order' => $row[0],
                        'note' => $row[1],
                        'amount_to_collect' => $row[2],
                    ]);
                }*/
    }
