<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class goodreceiptModel extends Model
{
    use HasFactory;

    protected $table = "goods_receipt";

    protected $fillable = [
        'po',
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
}
