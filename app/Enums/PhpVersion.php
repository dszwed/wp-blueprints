<?php

declare(strict_types=1);

namespace App\Enums;

/** @typescript **/
enum PhpVersion: string
{
    case V8_2 = '8.2';
    case V8_1 = '8.1';
    case V8_0 = '8.0';
    case V7_4 = '7.4';
    case V7_3 = '7.3';
    case V7_2 = '7.2';
    case V7_1 = '7.1';
    case V7_0 = '7.0';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
} 