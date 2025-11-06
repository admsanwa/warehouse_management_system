<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BonDetailsModel extends Model
{
    use HasFactory;

    protected $table = 'bon_details';

    protected $fillable = ['bon_id', 'item_code', 'item_name', 'qty', 'uom', 'remark'];

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

    // Relasi ke GRPO lewat BON (via IO)
    public function grpo()
    {
        return $this->hasOneThrough(
            grpoModel::class,   // Model tujuan
            BonModel::class,    // Model perantara
            'id',               // Foreign key di bon (ke bon_details.bon_id)
            'no_po',               // Foreign key di grpo (ke bon.io)
            'bon_id',           // Local key di bon_details
            'no_po'                // Local key di bon
        )->whereColumn('bon.no_series', 'grpo.no_series');
    }
}
