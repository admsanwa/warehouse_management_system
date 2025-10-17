<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class PrepareMatModel extends Model
{
    use HasFactory;

    protected $table = "prepare_material";

    protected $fillable = ['doc_entry', 'io', 'series', 'doc_num', 'prod_no', 'item_code', 'prepare_qty', 'status'];

    static public function getRecord()
    {
        $sub    = self::selectRaw('MAX(id) as id')->groupBy('doc_entry');
        $query = self::whereIn('id', $sub);

        // search box start
        if (!empty(Request::get('io'))) {
            $query->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }

        if (Request::filled('series')) {
            $query->where('series', Request::get('series'));
        }

        if (!empty(Request::get('doc_num'))) {
            $query->where('doc_num', 'LIKE', '%' . Request::get('doc_num') . '%');
        }

        if (!empty(Request::get('prod_no'))) {
            $query->where('prod_no', 'LIKE', '%' . Request::get('prod_no') . '%');
        }

        if (Request::filled('status')) {
            $query->where('status', Request::get('status'));
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }
}
