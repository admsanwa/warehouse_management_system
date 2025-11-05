<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class DeliveryModel extends Model
{
    use HasFactory;

    protected $table = "delivery";
    protected $fillable = ['doc_entry', 'io', 'prod_order', 'prod_no', 'prod_desc', 'status', 'series', 'remark', 'tracker_by'];

    static public function getRecord()
    {
        $sub = self::selectRaw('MAX(id) AS id')->groupBy('prod_no');
        $return = self::whereIn('id', $sub);

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
        if (!empty(Request::get('status'))) {
            $return = $return->where('status', 'LIKE', '%' . Request::get('status') . '%');
        }
        if (!empty(Request::get('series'))) {
            $return = $return->where('series', '=', Request::get('series'));
        }

        return $return->orderBy('id', 'desc');
    }

    static public function getRecordTwo()
    {
        $return = self::select('delivery.*');

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
        if (!empty(Request::get('status'))) {
            $return = $return->where('status', 'LIKE', '%' . Request::get('status') . '%');
        }

        return $return->orderBy('id', 'desc');
    }

    public function getShouldHighlightAttribute()
    {
        return $this->is_temp == 0 || is_null($this->status) || $this->status === '';
    }
}
