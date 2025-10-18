<?php

namespace App\Services\Payment\Drivers;

use App\Exceptions\BaseException;
use App\Services\Payment\Contracts\PaymentDriver;

use App\Traits\ExceptionTrait;
use SoapClient;
use SoapFault;

class SamanDriver implements PaymentDriver
{
    use ExceptionTrait;

    private string $initWsdl;
    private string $verifyWsdl;
    private string $merchantCode;

    private ?SoapClient $initClient = null;
    private ?SoapClient $verifyClient = null;

    /**
     * SamanDriver constructor.
     *
     * @param string $merchantCode Merchant code provided by Saman
     */
    public function __construct(string $merchantCode)
    {
        $this->merchantCode = $merchantCode;
        $this->initWsdl = config('payment.saman.init_wsdl');
        $this->verifyWsdl = config('payment.saman.verify_wsdl');
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function pay(int $transactionId, int $amount, string $callBackUrl, string $currency = 'T'): array
    {
        $amountInRial = $currency === 'T' ? $amount * 10 : $amount;

        try {
            $soapClient = $this->getInitClient();
            $result = $soapClient->RequestToken($this->merchantCode, $transactionId, $amountInRial, $callBackUrl);

            if (!$result || $result <= 0) {
                return [
                    'success' => false,
                    'message' => __('transaction.connection_failed', ['gateway' => 'سامان'])
                ];
            }

            return [
                'success' => true,
                'message' => __('transaction.payment_initiated'),
                'redirect_url' => "https://sep.shaparak.ir/Payment.aspx?Token={$result}",
                'token' => $result,
            ];

        } catch (SoapFault|\Throwable $e) {
            $this->throwServerError($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function verify(array $data): array
    {

        $refNum = $data['RefNum'] ?? null;
        $state = $data['State'] ?? null;

        if (!$refNum || $state !== 'OK') {
            return [
                'success' => false,
                'message' => __('transaction.payment_failed'),
                'refId' => $refNum,
            ];
        }

        try {
            $soapClient = $this->getVerifyClient();
            $result = $soapClient->VerifyTransaction($refNum, $this->merchantCode);

            if (!$result || $result <= 0) {
                return [
                    'success' => false,
                    'message' => __('transaction.payment_failed'),
                    'refId' => $refNum,
                ];
            }

            return [
                'success' => true,
                'message' => __('transaction.payment_verified'),
                'refId' => $refNum,
            ];

        } catch (SoapFault|\Throwable $e) {
            $this->throwServerError($e->getMessage());
        }
    }

    /**
     * Returns a cached SoapClient for payment initiation.
     *
     * @return SoapClient
     * @throws SoapFault
     */
    private function getInitClient(): SoapClient
    {
        if ($this->initClient === null) {
            $this->initClient = new SoapClient($this->initWsdl);
        }
        return $this->initClient;
    }

    /**
     * Returns a cached SoapClient for transaction verification.
     *
     * @return SoapClient
     * @throws SoapFault
     */
    private function getVerifyClient(): SoapClient
    {
        if ($this->verifyClient === null) {
            $this->verifyClient = new SoapClient($this->verifyWsdl);
        }
        return $this->verifyClient;
    }
}
