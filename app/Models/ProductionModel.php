<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class ProductionModel extends Model
{
    use HasFactory;

    protected $table = 'production_order';

    static public function getRecord()
    {
        $return = self::select('production_order.*');

        if (!empty(Request::get('prod_no'))) {
            $return = $return->where('prod_no', 'LIKE', '%' . Request::get('prod_no') . '%');
        }
        if (!empty(Request::get('prod_desc'))) {
            $return = $return->where('prod_desc', 'LIKE', '%' . Request::get('prod_desc') . '%');
        }
        if (!empty(Request::get('no_io'))) {
            $return = $return->where('no_io', 'LIKE', '%' . Request::get('no_io') . '%');
        }
        if (!empty(Request::get('doc_num'))) {
            $return = $return->where('doc_num', 'LIKE', '%' . Request::get('doc_num') . '%');
        }

        $return = $return->orderBy('id', 'desc')->paginate(5);
        return $return;
    }

    public function scopeFilter($query, $request)
    {
        if (!empty($request->prod_no)) {
            $query->where('prod_no', 'LIKE', '%' . $request->prod_no . '%');
        }

        if (!empty($request->prod_desc)) {
            $query->where('prod_desc', 'LIKE', '%' . $request->prod_desc . '%');
        }

        if (!empty($request->io_no)) {
            $query->where('io_no', 'LIKE', '%' . $request->io_no . '%');
        }

        if (!empty($request->result)) {
            $query->whereHas('qualityTwo', function ($q) use ($request) {
                $q->where('result', $request->result);
            });
        }

        if (!empty($request->status)) {
            $query->whereHas('deliveryTwo', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        return $query;
    }

    public function stocks()
    {
        return $this->belongsTo(StockModel::class, "doc_num", "prod_order");
    }

    public function po_details()
    {
        return $this->belongsTo(ProductionOrderDetailsModel::class, "doc_num", "doc_num");
    }

    public function quality()
    {
        return $this->belongsTo(QualityModel::class, "io_no", "io");
    }

    public function delivery()
    {
        return $this->belongsTo(DeliveryModel::class, "io_no", "io");
    }

    public function qualityTwo()
    {
        return $this->hasOne(QualityModel::class, "io", "io_no")->latestOfMany();
    }

    public function deliveryTwo()
    {
        return $this->hasOne(DeliveryModel::class, "io", "io_no")->latestOfMany();
    }
}
