<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Order::where('status', 'completed')->select('id', 'user_id', 'product_id', 'quantity', 'total_price', 'status', 'created_at')->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'User ID',
            'Product ID',
            'Quantity',
            'Total Price',
            'Status',
            'Created At',
        ];
    }
}
