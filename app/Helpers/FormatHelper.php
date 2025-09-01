<?php

if (!function_exists('formatDecimalsSAP')) {
    /**
     * Format angka:
     * - Jika bulat → tanpa desimal
     * - Jika ada pecahan → tampil 2 desimal
     * - Gunakan koma (,) untuk desimal, titik (.) untuk ribuan
     *
     * @param float|int $value
     * @return string
     */
    function formatDecimalsSAP($value)
    {
        if ($value === null || $value === '') {
            return '';
        }

        $stringVal = (string)$value;

        if (strpos($stringVal, '.') !== false) {
            $decimals = strlen($stringVal) - strpos($stringVal, '.') - 1;
        } else {
            $decimals = 0;
        }
        return number_format($value, $decimals, ',', '.');
    }
}
