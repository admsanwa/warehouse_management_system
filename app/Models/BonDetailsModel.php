<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonDetailsModel extends Model
{
    use HasFactory;

    protected $table = 'bon_details';

    protected $fillable = ['bon_id', 'item_code', 'item_name', 'qty', 'uom', 'remark'];
}
