<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grpoModel extends Model
{
    use HasFactory;

    protected $table = 'grpo';

    protected $fillable = [
        'no_po',
        'base_entry',
        'line_num',
        'item_code',
        'item_desc',
        'vendor_code',
        'user_id',
        'vendor',
        'vendor_ref_no',
        'io',
        'so',
        'internal_no',
        'whse',
        'note',
        'remarks'
    ];

    public function bon()
    {
        return $this->belongsTo(BonModel::class, 'io', 'io');
    }
}
