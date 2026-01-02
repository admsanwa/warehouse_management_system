<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonDetailsModel extends Model
{
    use HasFactory;

    protected $table = 'bon_details';

    protected $fillable = ['bon_id', 'item_code', 'item_name', 'qty', 'uom', 'remark', 'no_series'];

    public function scopeFilter($query)
    {
        // Filter berdasarkan tanggal dari relasi bon
        if (request('start_date') && request('end_date')) {
            $query->whereHas('bon', function ($q) {
                $q->whereBetween('date', [request('start_date'), request('end_date')]);
            });
        }

        return $query;
    }

    public function bon()
    {
        return $this->belongsTo(BonModel::class, 'bon_id', 'id');
    }

    public function grpo()
    {
        return $this->hasMany(grpoModel::class, 'no_series', 'no_series');
    }
}
