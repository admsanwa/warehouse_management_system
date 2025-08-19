<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoDetailModel extends Model
{
    use HasFactory;

    protected $table = 'memo_details';
    protected $fillable = ['memo_id', 'needs', 'unit', 'width', 'height', 'qty', 'uom'];
}
