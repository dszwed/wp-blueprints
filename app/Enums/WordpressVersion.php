<?php

declare(strict_types=1);

namespace App\Enums;

/** @typescript **/
enum WordpressVersion: string
{
    case V6_8 = '6.8';
    case V6_7 = '6.7';
    case V6_6 = '6.6';
    case V6_5 = '6.5';
    case V6_4 = '6.4';
    case V6_3 = '6.3';
    case V6_2 = '6.2';
    case V6_1 = '6.1';
    case V6_0 = '6.0';
    case V5_9 = '5.9';
    case V5_8 = '5.8';
    case V5_7 = '5.7';
    case V5_6 = '5.6';
    case V5_5 = '5.5';
    case V5_4 = '5.4';
    case V5_3 = '5.3';
    case V5_2 = '5.2';
    case V5_1 = '5.1';
    case V5_0 = '5.0';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
} 