<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
        return DB::transaction(function () {

            $now = Carbon::now();
            $month = $now->month;
            $year = substr($now->year, -2);
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
            // Get max number for current month & year
            $lastNumber = MemoModel::where('no', 'LIKE', "%/P/PPIC/{$romanMonth}/{$year}%")
                ->lockForUpdate()
                ->selectRaw("MAX(CAST(SUBSTRING_INDEX(no, '/', 1) AS UNSIGNED)) AS max_no")
                ->value('max_no');

            $number = ($lastNumber ?? 0) + 1;

            // Ensure no duplicate (safety check)
            $noMemo = "{$number}/P/PPIC/{$romanMonth}/{$year}";

            // Final safety check (should be false due to lock)
            if (MemoModel::where('no', $noMemo)->exists()) {
                throw new \Exception("Duplicate memo number detected");
            }

            return $noMemo;
        });
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
