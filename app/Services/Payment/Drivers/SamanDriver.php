<?php

namespace App\Services\Payment\Drivers;

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
                    'message' => 'Error connecting to Saman gateway',
                    'redirect_url' => null,
                    'token' => null,
                ];
            }

            return [
                'success' => true,
                'message' => 'Payment request created successfully.',
                'redirect_url' => "https://sep.shaparak.ir/Payment.aspx?Token={$result}",
                'token' => $result,
            ];

        } catch (SoapFault|\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'redirect_url' => null,
                'token' => null,
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function verify(array $data): array
    {
        $refNum = $data['RefNum'] ?? null;

        if (!$refNum) {
            return [
                'success' => false,
                'message' => 'Transaction reference number is missing',
                'token' => null,
                'refId' => null,
            ];
        }

        try {
            $soapClient = $this->getVerifyClient();
            $result = $soapClient->VerifyTransaction($refNum, $this->merchantCode);

            if (!$result || $result <= 0) {
                return [
                    'success' => false,
                    'message' => 'Transaction failed',
                    'token' => $refNum,
                    'refId' => null,
                ];
            }

            return [
                'success' => true,
                'message' => 'Transaction successfully verified',
                'token' => $refNum,
                'refId' => $result,
            ];

        } catch (SoapFault|\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'token' => $refNum,
                'refId' => null,
            ];
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
