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

        // Kalau bulat (misal 49.00 → 49)
        if (fmod($value, 1) == 0) {
            return number_format($value, 0, ',', '.');
        }

        // Kalau pecahan, tampilkan 2 desimal
        return number_format($value, 2, ',', '.');
    }
}
