<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SapReasonIssueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reasons = [
            '01'  => 'Adjustment Stock Opname',
            '02'  => 'Terima kembali Item Scrap',
            '03'  => 'Terima hasil proses maklon',
            '04'  => 'Simple Conversion',
            '05'  => 'Production Order',
            '06'  => 'Opbal (Good In Transit)',
            '07'  => 'Adj Stock (Input GL Acct manual)',
            '08'  => 'Garansi',
            '09'  => 'Sample',
            '10'  => 'Umum',
            '11'  => 'Others',
        ];

        $data = [];
        foreach ($reasons as $code => $desc) {
            $data[] = [
                'type'        => 'issue',
                'reason_code' => $code,
                'reason_desc' => $desc,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        DB::table('sap_reasons')->upsert(
            $data,
            ['reason_code', 'type'], // unique key
            ['reason_desc']          // update if exists
        );
    }
}
