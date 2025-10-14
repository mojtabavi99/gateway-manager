<?php

namespace App\Enums;

enum Gateway: int
{
    case SADAD = 1;
    case SAMAN = 2;
    case PASARGAD = 3;
    case PARSIAN = 4;
    case IRAN_KISH = 5;
    case BEH_PARDAKHT = 6;
    case ASAN_PARDAKHT = 7;
    case ZARINPAL = 8;
    case SNAPPPAY = 9;

    public function label(): string
    {
        return match ($this) {
            self::SADAD => 'سداد',
            self::SAMAN => 'سامان',
            self::PASARGAD => 'پاسارگاد',
            self::PARSIAN => 'پارسیان',
            self::IRAN_KISH => 'ایران کیش',
            self::BEH_PARDAKHT => 'ملت',
            self::ASAN_PARDAKHT => 'آسان پرداخت',
            self::ZARINPAL => 'زرین پال',
            self::SNAPPPAY => 'اسنپ پی',
        };
    }

    public function driver(): string
    {
        return match ($this) {
            self::SADAD => 'sadad',
            self::SAMAN => 'saman',
            self::PASARGAD => 'pasargad',
            self::PARSIAN => 'parsian',
            self::IRAN_KISH => 'iran_kish',
            self::BEH_PARDAKHT => 'beh_pardakht',
            self::ASAN_PARDAKHT => 'asan_pardakht',
            self::ZARINPAL => 'zarinpal',
            self::SNAPPPAY => 'snapppay',
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
