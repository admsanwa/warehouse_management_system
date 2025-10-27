<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class QualityModel extends Model
{
    use HasFactory;

    protected $table = "quality";

    protected $fillable = ["io", "result", "result_by"];

    static public function getRecord()
    {
        $return = self::select('quality.*');
        if (!empty(Request::get('prod_order'))) {
            $return = $return->where('prod_order', 'LIKE', '%' . Request::get('prod_order') . '%');
        }
        if (!empty(Request::get('prod_no'))) {
            $return = $return->where('prod_no', 'LIKE', '%' . Request::get('prod_no') . '%');
        }
        if (!empty(Request::get('prod_desc'))) {
            $return = $return->where('prod_desc', 'LIKE', '%' . Request::get('prod_desc') . '%');
        }
        if (!empty(Request::get('io'))) {
            $return = $return->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }
        if (!empty(Request::get('result'))) {
            $return = $return->where('result', 'LIKE', '%' . Request::get('result') . '%');
        }
        if (!empty(Request::get('status'))) {
            $return = $return->whereHas('delivery', function ($q) {
                $q->where('status', 'LIKE', '%' . Request::get('status') . '%');
            });
        }

        return $return->orderBy('id', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'result_by', 'username');
    }
}
