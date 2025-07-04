<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class QualityModel extends Model
{
    use HasFactory;

    protected $table = "quality";

    protected $fillable = ["io", "result"];
}
