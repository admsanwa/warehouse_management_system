<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IFPModel extends Model
{
    use HasFactory;

    protected $table = "ifp";

    protected $fillable = [
        'no_po',
        'io',
        'so',
        'project_code',
        'whse',
        'reason',
        'remarks'
    ];
}
