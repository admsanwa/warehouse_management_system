<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\StockModel;
use Request;

class ItemsModel extends Model
{
    use HasFactory;

    protected $table = 'items';

    static public function getRecord()
    {
        $return = self::select('items.*');

        // search box start
        if (!empty(Request::get('code'))) {
            $return = $return->where('code', 'LIKE', '%' . Request::get('code') . '%');
        }

        if (!empty(Request::get('name'))) {
            $return = $return->where('name', 'LIKE', '%' . Request::get('name') . '%');
        }

        $return = $return->orderBy('id', 'desc')->paginate(10);
        return $return;
    }

    static public function getRecordTwo($id)
    {
        return self::where('id', $id)->first();
    }

    public function stocks()
    {
        return $this->belongsTo(StockModel::class, 'code', 'item_code')->latest('id');
        // items.id -> stock.item_id
    }

    public function items_maklon()
    {
        return $this->hasMany(ItemsMaklonModel::class, 'code', 'code');
    }
}
