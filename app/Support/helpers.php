<?php

use Illuminate\Support\Carbon;

if (! function_exists('format_number')) {
    /**
     * Format angka menggunakan pemisah ribuan titik khas Indonesia (contoh: 1.250).
     */
    function format_number(int|float|null $number, int $decimals = 0): string
    {
        return number_format((float) ($number ?? 0), $decimals, ',', '.');
    }
}

if (! function_exists('format_date')) {
    /**
     * Format tanggal ke format Indonesia (contoh: 1 Feb 2026).
     */
    function format_date(DateTimeInterface|string|null $date): string
    {
        if (! $date) {
            return '-';
        }

        $carbon = $date instanceof DateTimeInterface
            ? Carbon::instance($date)
            : Carbon::parse($date);

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        return $carbon->day.' '.$months[$carbon->month].' '.$carbon->year;
    }
}

if (! function_exists('format_datetime')) {
    /**
     * Format tanggal dan waktu ke format Indonesia (contoh: 1 Feb 2026, 15:30).
     */
    function format_datetime(DateTimeInterface|string|null $datetime, bool $withSeconds = false): string
    {
        if (! $datetime) {
            return '-';
        }

        $carbon = $datetime instanceof DateTimeInterface
            ? Carbon::instance($datetime)
            : Carbon::parse($datetime);

        $timeFormat = $withSeconds ? 'H:i:s' : 'H:i';

        return format_date($carbon).', '.$carbon->format($timeFormat);
    }
}
