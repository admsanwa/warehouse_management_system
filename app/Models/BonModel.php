<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sign;
use Request;

class BonModel extends Model
{
    use HasFactory;

    protected $table = "bon";
    protected $fillable = [
        'type',
        'no',
        'date',
        'section',
        'io',
        'no_po',
        'project',
        'make_to',
        'created_by'
    ];

    static public function getRecord()
    {
        $return = self::select('bon.*');

        // search box start
        if (!empty(Request::get('no'))) {
            $return = $return->where('no', 'LIKE', '%' . Request::get('no') . '%');
        }

        if (!empty(Request::get('project'))) {
            $return = $return->where('project', 'LIKE', '%' . Request::get('project') . '%');
        }

        if (!empty(Request::get('date'))) {
            $return = $return->where('date', 'LIKE', '%' . Request::get('date') . '%');
        }

        if (!empty(Request::get('io'))) {
            $return = $return->where('io', 'LIKE', '%' . Request::get('io') . '%');
        }

        return $return->orderBy('id', 'desc')->paginate(10);
    }

    public static function generateNumber()
    {
        $now        = Carbon::now();
        $month      = $now->month;
        $year       = $now->year;
        $lastBon    = BonModel::orderByDesc('id')->first();
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

        if ($lastBon && !empty($lastBon->no)) {
            $parts      = explode('/', $lastBon->no);
            $lastYear   = intval($parts[4]);

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
        return $this->hasMany(BonDetailsModel::class, 'bon_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'fullname');
    }

    public function signBon()
    {
        return $this->belongsTo(SignBonModel::class, 'no', 'no_bon');
    }

    public function grpo()
    {
        return $this->hasOne(grpoModel::class, 'no_po', 'no_po');
    }
}
