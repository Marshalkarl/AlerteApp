<?php
namespace App\Support;

class Phone
{
    public static function normalize(string $raw): string
    {
        $phone = preg_replace('/\s+/', '', $raw);
        return preg_match('/^\d{8}$/', $phone) ? '+228' . $phone : $phone;
    }
}