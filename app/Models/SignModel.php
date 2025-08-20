<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignModel extends Model
{
    use HasFactory;

    protected $table = "sign";

    public function memo()
    {
        return $this->belongsTo(SignModel::class, 'no_memo', 'no');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
}
