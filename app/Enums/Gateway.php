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
            self::SADAD => 'سداد',
            self::SAMAN => 'سامان',
            self::PASARGAD => 'پاسارگاد',
            self::PARSIAN => 'پارسیان',
            self::IRAN_KISH => 'ایران کیش',
            self::BEH_PARDAKHT => 'به پرداخت ملت',
            self::ASAN_PARDAKHT => 'آسان پرداخت',
            self::ZARINPAL => 'زرین پال',
            self::SNAPPPAY => 'اسنپ پی',
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
