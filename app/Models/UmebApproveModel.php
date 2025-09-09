<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UmebApproveModel extends Model
{
    use HasFactory;

    protected $table = 'umeb_approve';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
    ];

    protected $casts = [
        'id' => 'integer',
    ];
}
