<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class PurchasingModel extends Model
{
    use HasFactory;

    protected $table = 'purchase_order';

    protected $fillable = ['no_po', 'vendor', 'contact_person', 'buyer', 'posting_date', 'status', 'item_code', 'item_type', 'item_desc', 'qty', 'uom'];

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
        if (!empty(Request::get('posting_date'))) {
            $return = $return->where('posting_date', 'LIKE', '%' . Request::get('posting_date') . 'posting_date');
        }

        $return = $return->orderBy('id', 'desc')->paginate(10);
        return $return;
    }

    public function stocks()
    {
        return $this->belongsTo(StockModel::class, "no_po", "no_po");
    }

    public function items_maklon()
    {
        return $this->belongsTo(ItemsMaklonModel::class);
    }

    public function po_details()
    {
        return $this->belongsTo(PurchaseOrderDetailsModel::class, "id", "po_id");
    }

    public function maklon_details()
    {
        return $this->belongsTo(ItemsMaklonModel::class, "no_po", "po");
    }
}
