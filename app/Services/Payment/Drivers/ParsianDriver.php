<?php

namespace App\Services\Payment\Drivers;

use App\Exceptions\BaseException;
use App\Services\Payment\Contracts\PaymentDriver;
use App\Traits\ExceptionTrait;
use SoapClient;
use SoapFault;

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
     * @throws BaseException
     */
    public function pay(int $transactionId, int $amount, string $callBackUrl, string $currency = 'T'): array
    {
        $amountInRial = $currency === 'T' ? $amount * 10 : $amount;

        try {
            $client = $this->getInitClient();

            $payload = [
                'LoginAccount' => $this->merchantCode,
                'Amount' => $amountInRial,
                'OrderId' => $transactionId,
                'CallBackUrl' => $callBackUrl,
            ];

            $result = $client->SalePaymentRequest(['requestData' => $payload]);

            $token = $result->SalePaymentRequestResult->Token ?? null;
            $status = (string)($result->SalePaymentRequestResult->Status ?? -1);
            $message = $result->SalePaymentRequestResult->Message ?? __('transaction.connection_failed', ['gateway' => 'پارسیان']);

            if ($status == '0' && $token) {
                return [
                    'success' => true,
                    'message' => __('transaction.payment_initiated'),
                    'redirect_url' => "https://pec.shaparak.ir/NewIPG/?Token={$token}",
                    'token' => $token,
                ];
            }

            return [
                'success' => false,
                'message' => $message,
            ];

        } catch (SoapFault|BaseException $e) {
            $this->throwServerError($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function verify(array $data): array
    {
        $token = $data['Token'] ?? '';
        $orderId = $data['OrderId'] ?? '';
        $status = $data['status'] ?? '';
        $amount = $data['Amount'] ?? '';
        $rrn = $data['RNN'] ?? '';

        if ($status != 0 || !$token) {
            return [
                'success' => false,
                'message' => __('transaction.payment_failed'),
                'token' => null,
                'refId' => null,
            ];
        }

        try {
            $client = $this->getVerifyClient();

            $params = [
                'LoginAccount' => $this->merchantCode,
                'Token' => $token,
                'OrderId' => $orderId,
                'Amount' => $amount,
            ];

            $result = $client->ConfirmPaymentWithAmount(['requestData' => $params]);

            $status = (string)($result->ConfirmPaymentWithAmountResult->Status ?? -1);
            $rrn = $result->ConfirmPaymentWithAmountResult->RRN ?? null;
            $message = $result->ConfirmPaymentResult->Message ?? __('transaction.verify_payment_failed');

            return [
                'success' => $status == '0',
                'message' => $message,
                'refId' => $rrn,
            ];

        } catch (SoapFault|BaseException $e) {
            $this->throwServerError($e->getMessage());
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
