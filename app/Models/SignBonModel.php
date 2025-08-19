<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignBonModel extends Model
{
    use HasFactory;

    protected $table = "sign_bon";

    public function user()
    {
        return $this->belongsTo(User::class, 'nik', 'nik');
    }
}
