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

        // Kondisi 1: kalau kosong → belum dimulai
        if (empty($from) && empty($to)) {
            return [
                'stage'            => 'Belum Dimulai',
                'status'           => 'Not Started',
                'progress_percent' => 0,
            ];
        }

        // Cari di rules
        foreach (self::rules() as $rule) {
            if ($from === $rule['from'] && $to === $rule['to']) {
                return [
                    'stage'            => $rule['stage'],
                    'status'           => $rule['status'],
                    'progress_percent' => $rule['percent'],
                ];
            }
        }

        // Kondisi 2: ada value tapi gak ada di rules
        return [
            'stage'            => 'Tidak Masuk Prosedur',
            'status'           => 'Forbidden',
            'progress_percent' => 0,
        ];
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
        // Tambah status khusus
        $statuses['Not Started'] = 'Not Started';
        $statuses['Forbidden']   = 'Forbidden';

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
        // Tambah stage khusus
        $stages['Belum Dimulai']        = 'Belum Dimulai';
        $stages['Tidak Masuk Prosedur'] = 'Tidak Masuk Prosedur';

        return array_values($stages);
    }
}
