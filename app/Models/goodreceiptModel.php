<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class goodreceiptModel extends Model
{
    use HasFactory;

    protected $table = "goods_receipt";

    protected $fillable = [
        'po',
        'no_series',
        'io',
        'internal_no',
        'so',
        'no_gi',
        'no_surat_jalan',
        'ref_surat_jalan',
        'no_inventory_tf',
        'type_inv_transaction',
        'reason',
        'whse',
        'project_code',
        'distr_rule',
        'vendor_code',
        'acct_code',
        'remarks'
    ];

    static public function getRecord()
    {
        $sub    = self::selectRaw('MAX(id) as id')->groupBy('doc_entry');
        $query = self::whereIn('id', $sub);

        // search box start
        if (!empty(Request::get('io'))) {
            $query->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }

        if (!empty(Request::get('po'))) {
            $query->where('po', 'LIKE', '%' . Request::get('po') . '%');
        }

        if (!empty(Request::get('so'))) {
            $query->where('so', 'LIKE', '%' . Request::get('so') . '%');
        }

        if (!empty(Request::get('internal_no'))) {
            $query->where('internal_no', 'LIKE', '%' . Request::get('internal_no') . '%');
        }

        return $query->orderBy('id', 'desc')->paginate(10);
    }
}
