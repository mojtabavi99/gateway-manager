<?php

namespace App\Services;

use App\Exceptions\BaseException;
use App\Repositories\TransactionRepository;
use App\Traits\ExceptionTrait;

class TransactionService extends Service
{
    use ExceptionTrait;

    protected UserService $userService;
    protected TransactionRepository $transactionRepository;

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
     * @throws BaseException
     */
    public function createUserAndTransaction(array $data)
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

        return $this->response->success('', $transaction->toArray());
    }

    /**
     * @throws BaseException
     */
    public function requestToIpg(array $data)
    {
        if (empty($data['transaction_id']) || empty($data['amount'])) {
            $this->throwValidation('');
        }
    }
}


//$client = new \SoapClient("https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL");
//
//$params = [
//    'LoginAccount' => 'YOUR_MERCHANT_CODE',
//    'Amount' => $data['amount'],
//    'OrderId' => $data['transaction_id'],
//    'CallBackUrl' => route('transaction-callback', $data['transaction_id']),
//];
//
//$response = $client->SalePaymentRequest($params);
//
//if ($response->SalePaymentRequestResult->Status == 0) {
//    return $this->response->success('', [
//        'token' => $response->SalePaymentRequestResult->Token,
//    ]);
//} else {
//    return $this->response->error($response->SalePaymentRequestResult->Message);
//}
