<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class DeliveryModel extends Model
{
    use HasFactory;

    protected $table = "delivery";

    static public function getRecord()
    {
        $query = self::with('production');

        if (!empty(Request::get('prod_no'))) {
            $query = $query->whereHas('production', function ($q) {
                $q->where('prod_no', 'LIKE', '%' . Request::get('prod_no') . '%');
            });
        }
        if (!empty(Request::get('prod_desc'))) {
            $query = $query->whereHas('production', function ($q) {
                $q->where('prod_desc', 'LIKE', '%' . Request::get('prod_desc') . '%');
            });
        }
        if (!empty(Request::get('io'))) {
            $query = $query->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }
        if (!empty(Request::get('status'))) {
            $query = $query->where('status', 'LIKE', '%' . Request::get('status') . '%');
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }

    public function production()
    {
        return $this->belongsTo(ProductionModel::class, 'io', 'io_no')->latest("id");
    }
}
