<?php

namespace App\Helpers;

class ProgressHelper
{
    /**
     * Definisi rules stage → status → percent
     */
    protected static function rules()
    {
        return [
            // Gudang → Produksi
            ['from' => 'BK001', 'to' => 'BK901', 'stage' => 'Maklon (Transfer)', 'status' => 'Proses Maklon', 'percent' => 10],
            ['from' => 'BK001', 'to' => 'BK002', 'stage' => 'Produksi (Transfer)', 'status' => 'Production Process', 'percent' => 30],
            // Produksi → QC
            ['from' => 'BK002', 'to' => 'BK003', 'stage' => 'QC (Transfer)', 'status' => 'QC Check', 'percent' => 45],
            // QC → Packing
            ['from' => 'BK003', 'to' => 'BK002', 'stage' => 'Packing (Transfer)', 'status' => 'Packing Process', 'percent' => 60],
            // Produksi → Gudang (Terima Barang)
            ['from' => 'BK002', 'to' => 'BK001', 'stage' => 'Terima Barang (Transfer)', 'status' => 'Receipt From Production', 'percent' => 75],
            // Produksi → Sales Transit
            ['from' => 'BK001', 'to' => 'JK001', 'stage' => 'Transit Ke Warehouse Jakarta (Transfer)', 'status' => 'Transfer', 'percent' => 90],
            ['from' => 'BK001', 'to' => 'SB904', 'stage' => 'Transit Ke Warehouse Surabaya (Transfer)', 'status' => 'Transfer', 'percent' => 80],
            // Transit → Instalasi Jakarta
            ['from' => 'JK001', 'to' => 'JK901', 'stage' => 'Warehouse Sales Jakarta (Transfer)', 'status' => 'Transfer', 'percent' => 95],
            ['from' => 'JK901', 'to' => 'JK902', 'stage' => 'Instalasi (Transfer)', 'status' => 'Installed', 'percent' => 100],
            // Transit → Instalasi Surabaya
            ['from' => 'SB904', 'to' => 'SB001', 'stage' => 'Warehouse Surabaya (Transfer)', 'status' => 'Transfer', 'percent' => 90],
            ['from' => 'SB001', 'to' => 'SB901', 'stage' => 'Warehouse Sales Surabaya (Transfer)', 'status' => 'Transfer', 'percent' => 95],
            ['from' => 'SB901', 'to' => 'SB902', 'stage' => 'Instalasi (Transfer)', 'status' => 'Installed', 'percent' => 100],
        ];
    }

    /**
     * Deteksi stage berdasarkan from/to warehouse
     */
    public static function detectStage($row)
    {
        $from = strtoupper($row['FromWhsCode'] ?? '');
        $to   = strtoupper($row['ToWhsCode'] ?? '');

        $result = [
            'stage'            => 'Belum Dimulai',
            'status'           => 'Not Started',
            'progress_percent' => 0,
        ];

        foreach (self::rules() as $rule) {
            if ($from === $rule['from'] && $to === $rule['to']) {
                $result['stage']            = $rule['stage'];
                $result['status']           = $rule['status'];
                $result['progress_percent'] = $rule['percent'];
                return $result;
            }
        }

        return $result;
    }

    /**
     * Ambil daftar status unik untuk dropdown filter
     */
    public static function getStatusList()
    {
        $statuses = [];
        foreach (self::rules() as $rule) {
            $statuses[$rule['status']] = $rule['status'];
        }
        return array_values($statuses);
    }

    /**
     * Ambil daftar stage unik (opsional kalau mau filter by stage)
     */
    public static function getStageList()
    {
        $stages = [];
        foreach (self::rules() as $rule) {
            $stages[$rule['stage']] = $rule['stage'];
        }
        return array_values($stages);
    }
}
