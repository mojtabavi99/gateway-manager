<?php

namespace App\Enums;

enum Gateway: string
{
    case SADAD = 'sadad';
    case SAMAN = 'saman';
    case PARSIAN = 'parsian';
    case PASARGAD = 'pasargad';
    case ZARINPAL = 'zarinpal';
    case SNAPPPAY = 'snapppay';
    case IRAN_KISH = 'iran_kish';
    case BEH_PARDAKHT = 'beh_pardakht';
    case ASAN_PARDAKHT = 'asan_pardakht';

    public function label(): string
    {
        return match ($this) {
            self::SADAD => __('enums.gateway.sadad'),
            self::SAMAN => __('enums.gateway.saman'),
            self::PARSIAN => __('enums.gateway.parsian'),
            self::PASARGAD => __('enums.gateway.pasargad'),
            self::ZARINPAL => __('enums.gateway.zarinpal'),
            self::SNAPPPAY => __('enums.gateway.snapppay'),
            self::IRAN_KISH => __('enums.gateway.iran_kish'),
            self::BEH_PARDAKHT => __('enums.gateway.beh_pardakht'),
            self::ASAN_PARDAKHT => __('enums.gateway.asan_pardakht'),
        };
    }

    public static function values(): array
    {
        return [
            self::SADAD,
            self::SAMAN,
            self::PASARGAD,
            self::PARSIAN,
            self::IRAN_KISH,
            self::BEH_PARDAKHT,
            self::ASAN_PARDAKHT,
            self::ZARINPAL,
            self::SNAPPPAY,
        ];
    }
}
