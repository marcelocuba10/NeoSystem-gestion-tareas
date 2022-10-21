<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Admin\Entities\Products;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ProductsImport implements ToModel, WithUpserts
{
    public function uniqueBy()
    {
        return 'custom_code';
    }

    public function model(array $row)
    {

        /** the letters of the excel headers, put in lowercase and the spaces with an _ */
        return new Products([
            'custom_code' => $row[1],
            'name' => $row[2],
            'purchase_price' => $row[3],
            'sale_price' => $row[4],
        ]);
    }
}
