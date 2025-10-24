<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BarcodeModel extends Model
{
    use HasFactory;

    protected $table = 'barcode';
    protected $fillable = ['code', 'name', 'qty', 'date_po', 'username'];

    static public function getRecord()
    {
        $user = Auth::user()->username;
        $return = self::where('barcode.username', $user)
            ->select('code', 'name', 'qty')
            ->get();

        return $return;
    }

    public function latestStock()
    {
        return $this->hasOne(StockModel::class, "item_code", "code")->latest("id");
    }
}
