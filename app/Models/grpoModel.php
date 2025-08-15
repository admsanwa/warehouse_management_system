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
        'vendor_code',
        'vendor',
        'vendor_ref_no',
        'io',
        'so',
        'remarks'
    ];
}
