<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ItemsModel;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

class StockModel extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    static public function getRecord()
    {
        $latestIds = self::select(DB::raw("max(id) as id"))->groupBy("item_id");
        $query = self::with("item")->whereIn("id", $latestIds->pluck("id"))->orderBy("id", "desc");

        if (!empty(Request::get('item_code'))) {
            $query->whereHas('item', function ($q) {
                $q->where('code', 'LIKE', '%' . Request::get('item_code') . '%');
            });
        }

        if (!empty(Request::get('item'))) {
            $query->whereHas('item', function ($q) {
                $q->where('name', 'LIKE', '%' . Request::get('item') . '%');
            });
        }

        if (!empty(Request::get('item_desc'))) {
            $query->whereHas('item', function ($q) {
                $q->where('desc', 'LIKE', '%' . Request::get('item_desc') . '%');
            });
        }

        return $query->paginate(5);
    }

    public function item()
    {
        return $this->belongsTo(ItemsModel::class, 'item_id', 'id');
        // stock.item_id -> items.id
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'scanned_by', 'id');
    }
}
