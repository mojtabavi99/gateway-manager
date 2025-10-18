<?php

namespace App\Services;

use App\Enums\Status;
use App\Exceptions\BaseException;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\Payment\DriverManager;
use App\Traits\ExceptionTrait;
use Illuminate\Http\JsonResponse;

class TransactionService extends Service
{
    use ExceptionTrait;

    protected UserService $userService;
    protected TransactionRepository $transactionRepository;

    /**
     * @param UserService $userService
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(
        UserService           $userService,
        TransactionRepository $transactionRepository
    )
    {
        $this->userService = $userService;
        $this->transactionRepository = $transactionRepository;

        parent::__construct();
    }

    /**
     * @param array $data
     * @return array|JsonResponse
     * @throws BaseException
     */
    public function deposit(array $data): array|JsonResponse
    {
        if (empty($data['mobile']) || empty($data['amount'])) {
            $this->throwValidation('');
        }

        $createUserResponse = $this->userService->createUser($data);
        if (!$createUserResponse['success']) {
            return $this->response->error($createUserResponse['message']);
        }

        $transaction = $this->transactionRepository->create([
            'user_id' => $createUserResponse['data']['id'],
            'gateway' => $data['gateway'],
            'amount' => $data['amount'],
        ]);

        return $this->response->success(__('transaction.deposit_request_created'), $transaction->toArray());
    }

    /**
     * @param Transaction $transaction
     * @return array|JsonResponse
     * @throws BaseException
     */
    public function initiatePayment(Transaction $transaction): array|JsonResponse
    {
        try {
            $driverManager = new DriverManager();
            $driver = $driverManager->driver($transaction->gateway->value);

            $response = $driver->pay(
                $transaction->id,
                $transaction->amount,
                route('transaction.site.verify_payment', $transaction->id)
            );

            if ($response['success']) {
                $this->transactionRepository->update($transaction->id, [
                    'payment_id' => $response['token'],
                ]);

                return $this->response->success(__('transaction.payment_initiated'), $response);
            } else {
                return $this->response->error($response['message']);
            }
        } catch (\Exception $e) {
            $this->throwServerError($e->getMessage());
        }
    }

    /**
     * @throws BaseException
     */
    public function verifyPayment(Transaction $transaction, array $data): array|JsonResponse
    {
        try {
            $driverManager = new DriverManager();
            $driver = $driverManager->driver($transaction->gateway->value);

            $result = $driver->verify($data);

            $updated_transaction = $this->transactionRepository->update($transaction->id, [
                'referral_code' => $data['refId'],
                'status' => $result['success'] ? Status::SUCCESS : Status::FAILED,
            ]);

            return $this->response->success(__('transaction.payment_verified'), [
                'transaction' => $updated_transaction,
            ]);
        } catch (\Exception $e) {
            $this->transactionRepository->update($transaction->id, [
                'status' => Status::FAILED,
            ]);

            $this->throwServerError($e->getMessage());
        }
    }
}
