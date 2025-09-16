<?php

namespace App\Helpers;

class ProgressHelper
{
    public static $stages = [
        'gudang_to_produksi' => 5,
        'produksi' => 20,
        'qc' => 15,
        'packing' => 10,
        'gudang_receive_packing' => 5,
        'finance' => 5,
        'transfer_project' => 5,
        'delivery' => 15,
        'installation' => 20,
    ];

    public static function detectStage($row)
    {
        $from = strtoupper($row['FromWhsCode'] ?? '');
        $to = strtoupper($row['ToWhsCode'] ?? '');

        if ($from == 'BK001' && $to == 'BK0002') {
            return 'gudang_to_produksi';
        }
        if ($from == 'BK002' && $to == 'NK0003') {
            return 'qc';
        }
        return 'unknown';
    }

    public static function progressPercent($stage)
    {
        $sum = 0;
        foreach (self::$stages as $key => $value) {
            $sum += $value;
            if ($key == $stage) {
                return $sum;
            }
        }
        return 0;
    }
}
