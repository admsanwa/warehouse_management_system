<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderDetailsModel extends Model
{
    use HasFactory;

    protected $table = "purchase_order_details";

    public function stocks()
    {
        return $this->belongsTo(StockModel::class, "nopo", "no_po");
    }
}
