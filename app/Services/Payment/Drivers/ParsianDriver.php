<?php

namespace App\Services\Payment\Drivers;

use SoapClient;
use SoapFault;
use App\Exceptions\BaseException;
use App\Services\Payment\Contracts\PaymentDriver;
use App\Traits\ExceptionTrait;

class ParsianDriver implements PaymentDriver
{
    use ExceptionTrait;

    private string $initWsdl;
    private string $verifyWsdl;
    private string $merchantCode;

    private ?SoapClient $initClient = null;
    private ?SoapClient $verifyClient = null;

    public function __construct(string $merchantCode)
    {
        $this->merchantCode = $merchantCode;
        $this->initWsdl = config('payment.parsian.init_wsdl');
        $this->verifyWsdl = config('payment.parsian.verify_wsdl');
    }

    /**
     * @inheritDoc
     */
    public function pay(int $transactionId, int $amount, string $callBackUrl, string $currency = 'T'): array
    {
        $amountInRial = $currency === 'T' ? $amount * 10 : $amount;

        try {
            $client = $this->getInitClient();

            $params = [
                'LoginAccount' => $this->merchantCode,
                'Amount' => $amountInRial,
                'OrderId' => $transactionId,
                'CallBackUrl' => $callBackUrl,
            ];

            $result = $client->SalePaymentRequest(['requestData' => $params]);

            $token = $result->SalePaymentRequestResult->Token ?? null;
            $status = $result->SalePaymentRequestResult->Status ?? -1;
            $message = $result->SalePaymentRequestResult->Message ?? 'Error connecting to Parsian gateway';

            if ($status === 0 && $token) {
                return [
                    'success' => true,
                    'message' => 'Payment request created successfully.',
                    'redirect_url' => "https://pec.shaparak.ir/NewIPG/?Token={$token}",
                    'token' => $token,
                ];
            }

            return [
                'success' => false,
                'message' => $message,
                'redirect_url' => null,
                'token' => null,
            ];

        } catch (SoapFault|BaseException $e) {
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
        $token = $data['Token'] ?? null;
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Transaction token is missing.',
                'token' => null,
                'refId' => null,
            ];
        }

        try {
            $client = $this->getVerifyClient();

            $params = [
                'LoginAccount' => $this->merchantCode,
                'Token' => $token,
            ];

            $result = $client->ConfirmPayment(['requestData' => $params]);

            $status = $result->ConfirmPaymentResult->Status ?? -1;
            $rrn = $result->ConfirmPaymentResult->RRN ?? null;
            $message = $result->ConfirmPaymentResult->Message ?? 'Error verifying transaction';

            return [
                'success' => $status === 0,
                'message' => $message,
                'token' => $token,
                'refId' => $rrn,
            ];

        } catch (SoapFault|BaseException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'token' => $token,
                'refId' => null,
            ];
        }
    }

    /**
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
