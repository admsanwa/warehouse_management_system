<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class goodissueModel extends Model
{
    use HasFactory;

    protected $table = "goods_issue";

    protected $fillable = [
        'po',
        'no_series',
        'io',
        'internal_no',
        'so',
        'no_surat_jalan',
        'no_inventory_tf',
        'type_inv_transaction',
        'reason',
        'whse',
        'project_code',
        'distr_rule',
        'vendor_code',
        'remarks'
    ];
}
