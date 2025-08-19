<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeProductionModel extends Model
{
    use HasFactory;

    protected $table = 'barcodeqc';

    static public function getRecord()
    {
        $user = Auth::user()->username;
        $return = self::where('barcodeqc.username', $user)
            ->select('prod_no', 'prod_desc', 'qty')
            ->get();

        return $return;
    }
}
