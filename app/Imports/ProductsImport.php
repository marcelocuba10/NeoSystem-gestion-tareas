<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Admin\Entities\Products;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        /** the letters of the excel headers, put in lowercase and the spaces with an _ */
        return new Products([
            'custom_code' => $row['codigo'],
            'name' => $row['producto'],
            'purchase_price' => $row['agente'],
            'sale_price' => $row['consumidor_final'],
        ]);
    }
}
