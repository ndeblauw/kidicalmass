<?php

namespace App\Support\Build;

enum Stage: string
{
    case NotStarted = 'not-started';   // 🔴
    case InProgress = 'in-progress';   // 🟠
    case Good = 'good';                // 🟢
    case NotApplicable = 'nvt';        // ⚪
    case ToDecide = 'to-decide';       // ❓

    public static function fromEmoji(string $cell): self
    {
        return match (true) {
            str_contains($cell, '🟠'), str_contains($cell, '🟡') => self::InProgress,
            str_contains($cell, '🟢'), str_contains($cell, '✅') => self::Good,
            str_contains($cell, '⚪') => self::NotApplicable,
            str_contains($cell, '❓') => self::ToDecide,
            default => self::NotStarted, // 🔴, —, blank
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::NotStarted => '🔴',
            self::InProgress => '🟠',
            self::Good => '🟢',
            self::NotApplicable => '⚪',
            self::ToDecide => '❓',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => 'niet begonnen',
            self::InProgress => 'bezig',
            self::Good => 'goed',
            self::NotApplicable => 'n.v.t.',
            self::ToDecide => 'te beslissen',
        };
    }
}
