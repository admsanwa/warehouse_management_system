<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class RFPModel extends Model
{
    use HasFactory;

    protected $table = "receipt_from_production";

    static public function getRecord()
    {
        $return = self::select("receipt_from_production.*");

        if (!empty(Request::get('prod_no'))) {
            $return = $return->where('prod_no', 'LIKE', '%' . Request::get('prod_no') . '%');
        }

        if (!empty(Request::get('io'))) {
            $return = $return->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }

        if (!empty(Request::get('prod_order'))) {
            $return = $return->where('prod_order', 'LIKE', '%' . Request::get('prod_order') . '%');
        }
        if (!empty(Request::get('prod_desc'))) {
            $return = $return->where('prod_desc', 'LIKE', '%' . Request::get('prod_desc') . '%');
        }

        return $return->orderByDesc("id");
    }

    public function production()
    {
        return $this->belongsTo(ProductionModel::class, "prod_no", "prod_no");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "scanned_by", "username");
    }

    public function items()
    {
        return $this->belongsTo(ItemsModel::class, "prod_no", "code");
    }
}
