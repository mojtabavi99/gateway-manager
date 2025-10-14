<?php

namespace App\Services\Payment\Contracts;

interface PaymentDriver
{
    /**
     * Initiates a payment request.
     *
     * @param int $transactionId
     * @param int $amount
     * @param string $callBackUrl
     * @param string $currency
     * @return array{
     *     success: bool,
     *     message: ?string,
     *     token: ?string,
     *     redirect_url: ?string,
     * }
     */
    public function pay(int $transactionId, int $amount, string $callBackUrl, string $currency = 'T'): array;

    /**
     * Verifies a payment transaction.
     *
     * @param array $data
     * @return array{
     *     success: bool,
     *     message: string,
     *     token: ?string,
     *     refId: ?string,
     * }
     */
    public function verify(array $data): array;
}
