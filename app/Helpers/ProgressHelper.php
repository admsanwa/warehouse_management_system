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
        $to   = strtoupper($row['ToWhsCode'] ?? '');

        // default return
        $result = [
            'stage'           => 'unknown',
            'progress_percent' => 0
        ];

        // (BK001) Gudang → (BK002) Produksi (Transfer) = 5%
        if ($from === 'BK001' && $to === 'BK002') {
            $result['stage'] = 'Produksi (Transfer)';
            $result['progress_percent'] = 5;
            return $result;
        }

        // (BK002) Produksi (Issue for Production & Production) = 15%
        if ($from === 'BK002' && empty($to)) { // Issue for Production (keluar)
            $result['stage'] = 'Issue for Production & Production';
            $result['progress_percent'] = 15;
            return $result;
        }

        // (BK002) Produksi → (BK003) QC (Check & Repair sampai OK) = 15%
        if ($from === 'BK002' && $to === 'BK003') {
            $result['stage'] = 'QC (Check & Repair sampai OK) ';
            $result['progress_percent'] = 15;
            return $result;
        }

        // (BK003) QC → (BK002) Produksi Receipt Production Semi Finish Good & Packing Produksi = 15%
        if ($from === 'BK003' && $to === 'BK002') {
            $result['stage'] = ' Produksi Receipt Production Semi Finish Good & Packing Produksi';
            $result['progress_percent'] = 15;
            return $result;
        }

        // (BK002) Produksi → (BK001) Gudang (Serah terima hasil packing) = 5%
        if ($from === 'BK002' && $to === 'BK001') {
            $result['stage'] = 'Gudang (Serah Terima Barang Packing)';
            $result['progress_percent'] = 5;
            return $result;
        }

        // Cek Payment (Finance/AR) = 5%
        if ($row['Stage'] ?? '' === 'PAYMENT_CHECK') {
            $result['stage'] = 'Cek Payment (Finance/AR)';
            $result['progress_percent'] = 5;
            return $result;
        }

        // (BK001) Gudang → (JK001) Transfer Project = 5%
        if ($from === 'BK001' && $to === 'JK001') {
            $result['stage'] = 'Transfer Project';
            $result['progress_percent'] = 5;
            return $result;
        }

        // Delivery (Muat, Tracking, Kirim) = 15%
        if ($row['Stage'] ?? '' === 'DELIVERY') {
            $result['stage'] = 'Delivery';
            $result['progress_percent'] = 15;
            return $result;
        }

        // Instalasi = 20%
        if ($row['Stage'] ?? '' === 'INSTALLATION') {
            $result['stage'] = 'Instalasi';
            $result['progress_percent'] = 20;
            return $result;
        }

        return $result;
    }
    // public static function detectStage($row)
    // {
    //     $from = strtoupper($row['FromWhsCode'] ?? '');
    //     $to   = strtoupper($row['ToWhsCode'] ?? '');
    //     $stageFlag = strtoupper($row['Stage'] ?? '');

    //     // default return
    //     $result = [
    //         'stage'            => 'unknown',
    //         'progress_percent' => 0
    //     ];

    //     // (BK001) Gudang → (BK002) Produksi (Transfer) = 5%
    //     if ($from === 'BK001' && $to === 'BK002') {
    //         $result['stage'] = 'Produksi (Transfer)';
    //         $result['progress_percent'] = 5;
    //         return $result;
    //     }

    //     // (BK002) Produksi (Issue for Production & Production) = 15%
    //     if ($from === 'BK002' && empty($to)) {
    //         $result['stage'] = 'Issue for Production & Production';
    //         $result['progress_percent'] = 15;
    //         return $result;
    //     }

    //     // (BK002) Produksi → (BK003) QC (Check & Repair sampai OK) = 30%
    //     if ($from === 'BK002' && $to === 'BK003') {
    //         $result['stage'] = 'QC (Check & Repair sampai OK)';
    //         $result['progress_percent'] = 30;
    //         return $result;
    //     }

    //     // (BK003) QC → (BK002) Produksi Receipt Production Semi Finish Good & Packing Produksi = 45%
    //     if ($from === 'BK003' && $to === 'BK002') {
    //         $result['stage'] = 'Produksi Receipt Production Semi Finish Good & Packing Produksi';
    //         $result['progress_percent'] = 45;
    //         return $result;
    //     }

    //     // (BK002) Produksi → (BK001) Gudang (Serah terima hasil packing) = 50%
    //     if ($from === 'BK002' && $to === 'BK001') {
    //         $result['stage'] = 'Gudang (Serah Terima Barang Packing)';
    //         $result['progress_percent'] = 50;
    //         return $result;
    //     }

    //     // Cek Payment (Finance/AR) = 55%
    //     if ($stageFlag === 'PAYMENT_CHECK') {
    //         $result['stage'] = 'Cek Payment (Finance/AR)';
    //         $result['progress_percent'] = 55;
    //         return $result;
    //     }

    //     // (BK001) Gudang → (JK001) Transfer Project = 60%
    //     if ($from === 'BK001' && $to === 'JK001') {
    //         $result['stage'] = 'Transfer Project';
    //         $result['progress_percent'] = 60;
    //         return $result;
    //     }

    //     // Delivery (Muat, Tracking, Kirim) = 75%
    //     // if ($stageFlag === 'DELIVERY') {
    //     //     $result['stage'] = 'Delivery';
    //     //     $result['progress_percent'] = 75;
    //     //     return $result;
    //     // }

    //     // // Instalasi = 100%
    //     // if ($stageFlag === 'INSTALLATION') {
    //     //     $result['stage'] = 'Instalasi';
    //     //     $result['progress_percent'] = 100;
    //     //     return $result;
    //     // }

    //     return $result;
    // }
}
