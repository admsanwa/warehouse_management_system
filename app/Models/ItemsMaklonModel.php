<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class ItemsMaklonModel extends Model
{
    use HasFactory;

    protected $table = 'items_maklon';
    protected $fillable = ['id'];

    static public function getRecord()
    {
        $return = self::select("items_maklon.*");

        if (!empty(Request::get('po'))) {
            $return = $return->where('po', 'LIKE', '%' . Request::get('po') . '%');
        }

        if (!empty(Request::get('io'))) {
            $return = $return->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }

        if (!empty(Request::get('internal_no'))) {
            $return = $return->where('internal_no', 'LIKE', '%' . Request::get('internal_no') . '%');
        }

        if (!empty(Request::get('code'))) {
            $return = $return->where('code', 'LIKE', '%' . Request::get('code') . '%');
        }

        if (!empty(Request::get('name'))) {
            $return = $return->where('name', 'LIKE', '%' . Request::get('name') . '%');
        }

        return $return->orderByDesc("id");
    }

    public function user()
    {
        return $this->belongsTo(User::class, "scanned_by", "username");
    }

    public function good_issue()
    {
        return $this->belongsTo(goodissueModel::class, "po", "po");
    }

    public function good_receipt()
    {
        return $this->belongsTo(goodreceiptModel::class, "po", "po");
    }
}
