<?php

namespace App\Enums;

use function Laravel\Prompts\search;
use function Symfony\Component\String\s;

enum Status: int
{
    case ACTIVE = 101;
    case INCOMPLETE = 102;
    case INACTIVE = 103;
    case BANNED = 104;

    case DRAFT = 201;
    case PUBLISHED = 202;
    case DELETED = 203;

    case PREPARING = 301;
    case SENDING = 302;
    case DELIVERED = 303;

    case PENDING = 401;
    case SUCCESS = 402;
    case FAILED = 403;
    case REFUND = 404;
    case CANCELLED = 405;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('enums.status.active'),
            self::INCOMPLETE => __('enums.status.incomplete'),
            self::INACTIVE => __('enums.status.inactive'),
            self::BANNED => __('enums.status.banned'),
            self::DRAFT => __('enums.status.draft'),
            self::PUBLISHED => __('enums.status.published'),
            self::DELETED => __('enums.status.deleted'),
            self::PREPARING => __('enums.status.preparing'),
            self::SENDING => __('enums.status.sending'),
            self::DELIVERED => __('enums.status.delivered'),
            self::PENDING => __('enums.status.pending'),
            self::SUCCESS => __('enums.status.success'),
            self::FAILED => __('enums.status.failed'),
            self::REFUND => __('enums.status.refund'),
            self::CANCELLED => __('enums.status.cancelled'),
        };
    }

    public static function values(): array
    {
        return [
            self::ACTIVE,
            self::INCOMPLETE,
            self::INACTIVE,
            self::BANNED,
            self::DRAFT,
            self::PUBLISHED,
            self::DELETED,
            self::PREPARING,
            self::SENDING,
            self::DELIVERED,
            self::PENDING,
            self::SUCCESS,
            self::FAILED,
            self::REFUND,
            self::CANCELLED,
        ];
    }

    public static function userValues(): array
    {
        return [
            self::ACTIVE,
            self::INCOMPLETE,
            self::INACTIVE,
            self::BANNED,
        ];
    }

    public static function contentValues(): array
    {
        return [
            self::DRAFT,
            self::PUBLISHED,
            self::DELETED,
        ];
    }

    public static function orderValues(): array
    {
        return [
            self::PREPARING,
            self::SENDING,
            self::DELIVERED,
        ];
    }

    public static function paymentValues(): array
    {
        return [
            self::PENDING,
            self::SUCCESS,
            self::FAILED,
            self::REFUND,
            self::CANCELLED,
        ];
    }
}
