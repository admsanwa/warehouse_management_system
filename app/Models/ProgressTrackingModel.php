<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProgressTrackingModel extends Model
{
    protected $table = "progress_trackings";

    protected $fillable = [
        'no_io',
        'project_code',
        'prod_order_no',
        'current_stage',
        'progress_percent'
    ];
}
