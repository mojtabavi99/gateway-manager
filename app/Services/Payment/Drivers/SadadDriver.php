<?php

namespace App\Services\Payment\Drivers;

use App\Services\Payment\Contracts\PaymentDriver;
use App\Traits\ExceptionTrait;
use Illuminate\Support\Facades\Http;

class SadadDriver implements PaymentDriver
{
    use ExceptionTrait;

    private string $initWsdl;
    private string $verifyWsdl;
    private string $secretKey;
    private string $terminalId;
    private string $merchantCode;

    /**
     * SadadDriver constructor.
     *
     * @param string $secretKey Merchant secret key
     * @param string $terminalId Terminal ID provided by Sadad
     * @param string $merchantCode Merchant code provided by Sadad
     */
    public function __construct(string $secretKey, string $terminalId, string $merchantCode)
    {
        $this->merchantCode = $merchantCode;
        $this->terminalId = $terminalId;
        $this->secretKey = $secretKey;
        $this->initWsdl = config('payment.sadad.init_wsdl');
        $this->verifyWsdl = config('payment.sadad.verify_wsdl');
    }

    /**
     * @inheritDoc
     */
    public function pay(int $transactionId, int $amount, string $callBackUrl, string $currency = 'T'): array
    {
        try {
            $amountInRial = $currency === 'T' ? $amount * 10 : $amount;
            $signData = $this->encryptPkcs7("{$this->terminalId};{$transactionId};{$amountInRial}", $this->secretKey);

            $payload = [
                'TerminalId' => $this->terminalId,
                'MerchantId' => $this->merchantCode,
                'Amount' => $amountInRial,
                'SignData' => $signData,
                'ReturnUrl' => $callBackUrl,
                'LocalDateTime' => now()->format('Y/m/d H:i:s'),
                'OrderId' => $transactionId,
            ];

            $response = $this->callApi($this->initWsdl, $payload);

            $resCode = $response->ResCode ?? -1;
            $token = $response->Token ?? null;
            $message = $response->Description ?? __('transaction.connection_failed', 'سداد');

            if ($resCode == 0 && $token) {
                return [
                    'success' => true,
                    'message' => __('transaction.payment_initiated'),
                    'redirect_url' => "https://sadad.shaparak.ir/VPG/Purchase?Token={$token}",
                    'token' => $token,
                ];
            }

            return [
                'success' => false,
                'message' => $message,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function verify(array $data): array
    {
        $token = $data['token'] ?? null;
        $orderId = $data['OrderId'] ?? null;
        $resCode = $data['ResCode'] ?? null;

        if ($resCode != 0 || !$token || !$orderId) {
            return [
                'success' => false,
                'message' => 'Transaction was cancelled or encountered an error.',
                'token' => $token,
                'refId' => null,
            ];
        }

        try {
            $payload = [
                'Token' => $token,
                'SignData' => $this->encryptPkcs7($token, $this->secretKey),
            ];

            $response = $this->callApi($this->verifyWsdl, $payload);

            $resCode = $response->ResCode ?? -1;
            $refId = $response->RetrivalRefNo ?? null;
            $message = $response->Description ?? __('transaction.verify_payment_failed');

            return [
                'success' => $resCode == 0,
                'message' => $message,
                'refId' => $refId,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Encrypts data using PKCS7 method for Sadad API.
     *
     * @param string $data Data to encrypt
     * @param string $key Base64 encoded key
     * @return string Encrypted and base64-encoded string
     */
    private function encryptPkcs7(string $data, string $key): string
    {
        $decodedKey = base64_decode($key);
        $ciphertext = openssl_encrypt($data, "DES-EDE3", $decodedKey, OPENSSL_RAW_DATA);
        return base64_encode($ciphertext);
    }

    /**
     * Sends a POST request to Sadad API and returns decoded JSON response.
     *
     * @param string $url API endpoint URL
     * @param array $payload Payload data to send
     * @return object|null Decoded response object or null on failure
     */
    private function callApi(string $url, array $payload): ?object
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Content-Type' => 'application/json; charset=utf-8'])
                ->post($url, $payload);

            return json_decode($response->body());
        } catch (\Throwable $e) {
            return null;
        }
    }
}
