<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class JobsModel extends Model
{
    use HasFactory;


    static public function getRecord($request)
    {
        $return = self::select('jobs.*');

        // search box start
        if (!empty(Request::get('id'))) {
            $return =  $return->where('id', '=', Request::get('id'));
        }
        if (!empty(Request::get('job_title'))) {
            $return = $return->where('job_title', '=', Request::get('job_title'));
        }
        if (!empty(Request::get('min_salary'))) {
            $return = $return->where('min_salary', '=', Request::get('min_salary'));
        }
        if (!empty(Request::get('max_salary'))) {
            $return = $return->where('max_salary', '=', Request::get('max_salary'));
        }
        if (!empty(Request::get('start_date') && !empty(Request::get('end_date')))) {
            $return = $return->where('jobs.created_at', '>=', Request::get('start_date'))->where('jobs.created_at', '<=', Request::get('end_date'));
        }

        $return = $return->orderBy('id', 'desc')->paginate(5);
        return $return;
    }
}
