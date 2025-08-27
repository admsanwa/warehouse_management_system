<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SapReasonModel extends Model
{
    use HasFactory;


    protected $table = 'sap_reasons';

    protected $fillable = [
        'type',
        'reason_code',
        'reason_desc',
    ];


    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
