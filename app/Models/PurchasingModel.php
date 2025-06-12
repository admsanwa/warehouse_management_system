<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class PurchasingModel extends Model
{
    use HasFactory;

    protected $table = 'purchase_order';

    protected $fillable = ['no_po', 'vendor', 'contact_person', 'buyer', 'delivery_date', 'status', 'item_code', 'item_type', 'item_desc', 'qty', 'uom'];

    static public function getRecord()
    {

        $return = self::select('purchase_order.*');

        if (!empty(Request::get('no_po'))) {
            $return = $return->where('no_po', 'LIKE', '%' . Request::get('no_po') . '%');
        }
        if (!empty(Request::get('vendor'))) {
            $return = $return->where('vendor', 'LIKE', '%' . Request::get('vendor') . '%');
        }
        if (!empty(Request::get('contact_person'))) {
            $return = $return->where('contact_person', 'LIKE', '%' . Request::get('contact_person') . '%');
        }
        if (!empty(Request::get('delivery_date'))) {
            $return = $return->where('delivery_date', 'LIKE', '%' . Request::get('delivery_date') . 'delivery_date');
        }

        $return = $return->orderBy('id', 'desc')->paginate(5);
        return $return;
    }
}
