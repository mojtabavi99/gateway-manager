<?php

namespace App\Services;

use App\Enums\Gateway;
use App\Enums\Status;
use App\Exceptions\BaseException;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\Payment\DriverManager;
use App\Traits\ExceptionTrait;
use Illuminate\Http\JsonResponse;

class PaymentService extends Service
{
    use ExceptionTrait;

    protected TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;

        parent::__construct();
    }

    /**
     * @param Transaction $transaction
     * @return JsonResponse|array
     */
    public function initiate(Transaction $transaction): JsonResponse|array
    {
        try {
            $driverManager = new DriverManager();
            $driver = $driverManager->driver(Gateway::from($transaction->gateway->value)->driver());

            $response = $driver->pay($transaction->id, $transaction->amount, route('transaction-callback'));

            if ($response['success']) {
                $this->transactionRepository->update($transaction->id, [
                    'token' => $response['token'],
                ]);

                return $this->response->success(__('payment.payment_initiated'), $response);
            } else {
                return $this->response->error($response['message']);
            }
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage());
        }
    }

    /**
     * @throws BaseException
     */
    public function verify(array $data)
    {
        if (empty($data['status']) || empty($data['payment_id']) || empty($data['referral_code'])) {
            $this->throwValidation();
        }

        $transaction = $this->transactionRepository->findBy([
            'id' => $data['transaction_id'],
            'payment_id' => $data['payment_id'],
        ]);

        if (!$transaction) {
            $this->throwNotFound();
        }

        try {
            $driverManager = new DriverManager();
            $driver = $driverManager->driver(Gateway::from($transaction->gateway)->driver());

            $result = $driver->verify($data);

            $status = $result['success'] ? Status::SUCCESS : Status::FAILED;

            $updated_transaction = $this->transactionRepository->update($transaction->id, [
                'status' => $status,
                'referral_code' => $data['referral_code'],
            ]);

            return $this->response->success(__('payment.transaction_success'), [
                'transaction' => $updated_transaction,
            ]);
        } catch (\Exception $e) {
            $updated_transaction = $this->transactionRepository->update($transaction->id, [
                'status' => Status::FAILED,
            ]);

            return $this->response->error($e->getMessage(), [
                'transaction' => $updated_transaction,
            ]);
        }
    }
}
