<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeProductionModel extends Model
{
    use HasFactory;

    protected $table = 'barcode_prod';

    static public function getRecord()
    {
        $user = Auth::user()->username;
        $return = self::where('barcode_prod.username', $user)
            ->select('prod_no', 'prod_desc', 'qty')
            ->get();

        return $return;
    }

    public function po()
    {
        return $this->belongsTo(ProductionModel::class, "prod_no", "prod_no");
    }
}
