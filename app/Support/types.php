<?php
namespace App\Support;

class Types
{
    public const LABELS = ['inond' => 'Inondation', 'eau' => 'Coupure d’eau', 'elec' => 'Coupure d’électricité'];
    public const ICONS  = ['inond' => '🌊', 'eau' => '🚰', 'elec' => '⚡'];

    public static function label(string $t): string { return self::LABELS[$t] ?? $t; }
    public static function icon(string $t): string  { return self::ICONS[$t] ?? '⚠️'; }
}