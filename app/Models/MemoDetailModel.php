<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemoDetailModel extends Model
{
    use HasFactory;

    protected $table = 'memo_details';
    protected $fillable = ['memo_id', 'needs', 'unit', 'width', 'height', 'qty', 'uom'];

    public function scopeFilter($query)
    {
        // Filter berdasarkan tanggal dari relasi memo
        if (request('start_date') && request('end_date')) {
            $query->whereHas('memo', function ($q) {
                $q->whereBetween('date', [request('start_date'), request('end_date')]);
            });
        }

        return $query;
    }

    public function memo()
    {
        return $this->belongsTo(MemoModel::class, 'memo_id', 'id');
    }

    // Relasi ke gr lewat memo
    public function gr()
    {
        return $this->hasManyThrough(
            goodreceiptModel::class,   // Model tujuan
            MemoModel::class,    // Model perantara
            'id',               // Foreign key di memo (ke memo_details.memo_id)
            'po',               // Foreign key di gr (ke memo.po)
            'memo_id',           // Local key di memo_details
            'po'                // Local key di memo
        )
            ->whereColumn('memo.no_series', 'gr.no_series')
            ->whereColumn('memo.po', 'gr.po');
    }

    public function getTotalGrQtyAttribute()
    {
        return goodreceiptModel::query()
            ->where('no_series', $this->memo->no_series)
            ->where('po', $this->memo->po)
            ->where('item_code', $this->item_code)
            ->sum('qty');
    }
}
