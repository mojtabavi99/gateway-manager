<?php

namespace App\Traits;

use Random\RandomException;

trait Strings
{
    /**
     * Convert Persian/Arabic digits in the given string to English digits.
     *
     * @param string|null $input
     * @return string|null
     */
    public function convertToEnglishDigits(?string $input): ?string
    {
        if ($input === null) {
            return null;
        }

        $persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $englishDigits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        $converted = str_replace($persianDigits, $englishDigits, $input);

        return str_replace($arabicDigits, $englishDigits, $converted);
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $string
     * @param string $separator
     * @return string
     */
    public function slugify(string $string, string $separator = '-'): string
    {
        // Normalize characters and remove special characters
        $slug = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $string);
        // Replace whitespace with separator
        $slug = preg_replace('/[\s]+/u', $separator, $slug);
        // Lowercase the slug
        $slug = mb_strtolower($slug);

        return trim($slug, $separator);
    }

    /**
     * @throws RandomException
     */
    function generateRandomPhrase(
        int  $length,
        bool $upperCase = true,
        bool $lowerCase = true,
        bool $digits = true,
        bool $symbols = false
    ): string
    {
        $chars = '';

        if ($upperCase) $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($lowerCase) $chars .= 'abcdefghijklmnopqrstuvwxyz';
        if ($digits) $chars .= '0123456789';
        if ($symbols) $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';

        if (empty($chars)) {
            throw new \InvalidArgumentException('At least one character set must be true.');
        }

        $result = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $maxIndex)];
        }

        return $result;
    }

    function maskContact(string $contact): string
    {
        if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
            [$user, $domain] = explode('@', $contact);
            $length = strlen($user);
            if ($length <= 2) {
                $maskedUser = str_repeat('*', $length);
            } else {
                $maskedUser = substr($user, 0, 2) . str_repeat('*', $length - 2);
            }
            return $maskedUser . '@' . $domain;
        }

        if (preg_match('/^\d{10,}$/', $contact)) {
            return preg_replace('/(\d{3})\d{3}(\d{4})/', '$1***$2', $contact);
        }

        return $contact;
    }
}
