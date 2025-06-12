<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class ProductionModel extends Model
{
    use HasFactory;

    protected $table = 'production_order';

    static public function getRecord()
    {
        $return = self::select('production_order.*');

        if (!empty(Request::get('prod_no'))) {
            $return = $return->where('prod_no', 'LIKE', '%' . Request::get('prod_no') . '%');
        }
        if (!empty(Request::get('prod_desc'))) {
            $return = $return->where('prod_desc', 'LIKE', '%' . Request::get('prod_desc') . '%');
        }
        if (!empty(Request::get('no_io'))) {
            $return = $return->where('no_io', 'LIKE', '%' . Request::get('no_io') . '%');
        }
        if (!empty(Request::get('doc_num'))) {
            $return = $return->where('doc_num', 'LIKE', '%' . Request::get('doc_num') . '%');
        }

        $return = $return->orderBy('id', 'desc')->paginate(5);
        return $return;
    }
}
