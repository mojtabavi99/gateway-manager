<?php

namespace App\Services\Payment\Drivers;

use App\Exceptions\BaseException;
use SoapClient;
use SoapFault;
use App\Services\Payment\Contracts\PaymentDriver;
use App\Traits\ExceptionTrait;

class ZarinpalDriver implements PaymentDriver
{
    use ExceptionTrait;

    private string $merchantId;
    private string $callbackUrl;
    private string $sandboxWsdl;
    private string $productionWsdl;
    private bool $isSandbox;

    /**
     * ZarinpalDriver constructor.
     *
     * @param string $merchantId Merchant ID provided by Zarinpal
     * @param bool $isSandbox Whether to use sandbox mode (default false)
     */
    public function __construct(string $merchantId, bool $isSandbox = false)
    {
        $this->merchantId = $merchantId;
        $this->isSandbox = $isSandbox;
        $this->sandboxWsdl = config('payment.zarinpal.sandbox_wsdl');
        $this->productionWsdl = config('payment.zarinpal.production_wsdl');
    }


    /**
     * @inheritDoc
     * @throws BaseException
     */
    public function pay(int $transactionId, int $amount, string $callBackUrl, string $currency = 'T'): array
    {
        $amountInToman = $currency === 'R' ? $amount / 10 : $amount;

        try {
            $client = new SoapClient($this->isSandbox ? $this->sandboxWsdl : $this->productionWsdl);

            $params = [
                'MerchantID' => $this->merchantId,
                'Amount' => $amountInToman,
                'Description' => "Payment for transaction #{$transactionId}",
                'CallbackURL' => $callBackUrl,
            ];

            $response = $client->PaymentRequest($params);

            if ($response->Status == 100 || $response->Status == 101) {
                $url = $this->isSandbox
                    ? "https://sandbox.zarinpal.com/pg/StartPay/{$response->Authority}"
                    : "https://www.zarinpal.com/pg/StartPay/{$response->Authority}";

                return [
                    'success' => true,
                    'message' => __('transaction.payment_initiated'),
                    'redirect_url' => $url,
                    'token' => $response->Authority,
                ];
            }

            return [
                'success' => false,
                'message' => __('transaction.connection_failed', ['gateway' => 'زرین پال']),
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
        $authority = $data['Authority'] ?? null;
        $amount = $data['Amount'] ?? null;

        if (!$authority || !$amount) {
            return [
                'success' => false,
                'message' => __('transaction.payment_failed'),
                'token' => $authority,
                'refId' => null,
            ];
        }

        try {
            $client = new SoapClient($this->isSandbox ? $this->sandboxWsdl : $this->productionWsdl);

            $params = [
                'MerchantID' => $this->merchantId,
                'Authority' => $authority,
                'Amount' => $amount,
            ];

            $response = $client->PaymentVerification($params);

            if ($response->Status == 100 || $response->Status == 101) {
                return [
                    'success' => true,
                    'message' => __('transaction.payment_verified'),
                    'refId' => $response->RefID,
                ];
            }

            return [
                'success' => false,
                'message' => __('transaction.payment_failed'),
                'refId' => null,
            ];

        } catch (SoapFault|\Throwable $e) {
            $this->throwServerError($e->getMessage());
        }
    }
}
