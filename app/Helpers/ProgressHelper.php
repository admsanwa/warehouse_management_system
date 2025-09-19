<?php

namespace App\Helpers;

class ProgressHelper
{
    public static function detectStage($row)
    {
        $from = strtoupper($row['FromWhsCode'] ?? '');
        $to   = strtoupper($row['ToWhsCode'] ?? '');

        // default
        $result = [
            'stage'            => 'Tidak Ada',
            'status'           => null,
            'progress_percent' => 0
        ];

        // definisi rule by mapping
        $rules = [
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
            ['from' => 'SB904', 'to' => 'SB001', 'stage' => 'Warehouse Surabaya(Transfer)', 'status' => 'Transfer', 'percent' => 90],
            ['from' => 'SB001', 'to' => 'SB901', 'stage' => 'Warehouse Sales Surabaya (Transfer)', 'status' => 'Transfer', 'percent' => 95],
            ['from' => 'SB901', 'to' => 'SB902', 'stage' => 'Instalasi (Transfer)', 'status' => 'Installed', 'percent' => 100],
        ];
        // $rules2 = [
        //     // Gudang → Produksi
        //     ['from' => 'BK001', 'to' => 'BK002', 'stage' => 'Produksi (Transfer)', 'status' => 'Issue For Production', 'percent' => 30],
        //     // Produksi → QC
        //     ['from' => 'BK002', 'to' => 'BK003', 'stage' => 'QC (Transfer)', 'status' => 'QC Check', 'percent' => 15],
        //     // QC → Packing
        //     ['from' => 'BK003', 'to' => 'BK002', 'stage' => 'Packing (Transfer)', 'status' => 'Packing Barang', 'percent' => 15],
        //     // Produksi → Gudang (Terima Barang)
        //     ['from' => 'BK002', 'to' => 'BK001', 'stage' => 'Terima Barang (Transfer)', 'status' => 'Receipt From Production', 'percent' => 15],
        //     // Produksi → Sales Transit
        //     ['from' => 'BK001', 'to' => 'JK001', 'stage' => 'Transit Ke Warehouse Jakarta (Transfer)', 'status' => 'Transit Barang', 'percent' => 10],
        //     ['from' => 'BK001', 'to' => 'SB904', 'stage' => 'Transit Ke Warehouse Surabaya (Transfer)', 'status' => 'Transit Barang', 'percent' => 10],
        //     // Transit → Instalasi Jakarta
        //     ['from' => 'JK001', 'to' => 'JK901', 'stage' => 'Warehouse Sales Jakarta (Transfer)', 'status' => 'Transfer Warehouse', 'percent' => 5],
        //     ['from' => 'JK901', 'to' => 'JK902', 'stage' => 'Instalasi (Transfer)', 'status' => 'Instalasi', 'percent' => 10],
        //     // Transit → Instalasi Surabaya
        //     ['from' => 'SB904', 'to' => 'SB001', 'stage' => 'Warehouse Surabaya(Transfer)', 'status' => 'Transfer Warehouse', 'percent' => 5],
        //     ['from' => 'SB001', 'to' => 'SB901', 'stage' => 'Warehouse Sales Surabaya (Transfer)', 'status' => 'Transfer Warehouse', 'percent' => 5],
        //     ['from' => 'SB901', 'to' => 'SB902', 'stage' => 'Instalasi (Transfer)', 'status' => 'Instalasi', 'percent' => 5],
        // ];

        // cek rules
        foreach ($rules as $rule) {
            if ($from === $rule['from'] && $to === $rule['to']) {
                $result['stage']            = $rule['stage'];
                $result['status']           = $rule['status'];
                $result['progress_percent'] = $rule['percent'];
                return $result;
            }
        }

        return $result;
    }
}
