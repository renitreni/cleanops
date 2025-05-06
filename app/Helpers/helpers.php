<?php

if (!function_exists('normalizePhoneNumber')) {
    function normalizePhoneNumber($number) {
        $number = preg_replace('/\D+/', '', $number); // remove non-digits

        if (strpos($number, '09') === 0) {
            return '+63' . substr($number, 1);
        } elseif (strpos($number, '05') === 0) {
            return '+966' . substr($number, 1);
        }

        return $number;
    }
}