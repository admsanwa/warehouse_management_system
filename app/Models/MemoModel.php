<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Request;

class MemoModel extends Model
{
    use HasFactory;

    protected $table = "memo";
    protected $fillable = ['created_by', 'no', 'description', 'date', 'project', 'io', 'due_date'];

    static public function getRecord()
    {
        $return = self::select('memo.*');

        // search box start
        if (!empty(Request::get('no'))) {
            $return = $return->where('no', 'LIKE', '%' . Request::get('no') . '%');
        }

        if (!empty(Request::get('desc'))) {
            $return = $return->where('desc', 'LIKE', '%' . Request::get('desc') . '%');
        }

        if (!empty(Request::get('project'))) {
            $return = $return->where('project', 'LIKE', '%' . Request::get('project') . '%');
        }

        if (!empty(Request::get('io'))) {
            $return = $return->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }

        $return = $return->orderBy('id', 'desc')->paginate(10);
        return $return;
    }

    public static function generateNumber()
    {
        $now = Carbon::now();
        $month = $now->month;
        $year = $now->year;
        $lastMemo = MemoModel::orderByDesc('id')->first();
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $romanMonth = $romanMonths[$month];
        $shortYears = substr($year, -2);

        if ($lastMemo && !empty($lastMemo->no)) {
            $parts = explode('/', $lastMemo->no);
            $lastYear = intval($parts[4]);

            if ($lastYear == $shortYears) {
                $lastNumber = intval($parts[0]);
                $number = $lastNumber + 1;
            } else {
                $number = 1;
            }
        } else {
            $number = 1;
        }

        return "{$number}/P/PPIC/{$romanMonth}/{$shortYears}";
    }

    public function details()
    {
        return $this->hasMany(MemoDetailModel::class, 'memo_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'fullname');
    }

    public function sign()
    {
        return $this->belongsTo(SignModel::class, 'no', 'no_memo');
    }
}
