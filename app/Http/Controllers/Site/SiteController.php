<?php

namespace App\Http\Controllers\Site;

use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Services\TransactionService;
use App\Traits\ExceptionTrait;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class SiteController extends Controller
{
    use ExceptionTrait;

    protected PaymentService $paymentService;
    protected TransactionService $transactionService;

    public function __construct(
        PaymentService $paymentService,
        TransactionService $transactionService
    )
    {
        $this->paymentService = $paymentService;
        $this->transactionService = $transactionService;
    }

    public function index(): View
    {
        $gateways = PaymentGateway::query()->
            where('status', PaymentGateway::STATUS_ACTIVE)->get();

        return view('site.index', [
            'gateways' => $gateways,
        ]);
    }

    /**
     * @param DepositRequest $request
     * @return RedirectResponse
     * @throws BaseException
     */
    public function deposit(DepositRequest $request): RedirectResponse
    {
        $response = $this->transactionService->createUserAndTransaction($request->validated());
        if ($response['success']) {
            return redirect()->route('register-transaction', $response['data']['id']);
        } else {
            return redirect()->back()->with('danger', $response['message']);
        }
    }

    /**
     * @throws \Exception
     */
    public function registerTransaction(Transaction $transaction): Redirector|RedirectResponse
    {
        $response = $this->paymentService->initiate($transaction);
        dd($response);
        if ($response['success']) {
            return redirect($response['data']['url']);
        }

        return redirect()->back()->with('danger', $response['message']);
    }

    /**
     * @param Request $request
     * @return Factory|View
     * @throws BaseException
     */
    public function transactionCallback(Request $request): Factory|View
    {
        $data = [
            'status' => $request->input('Status'),
            'payment_id' => $request->input('Token'),
            'referral_code' => $request->input('RNN'),
            'transaction_id' => $request->input('OrderId'),
        ];

        $verifyResponse = $this->paymentService->verify($data);

        return view('site.transaction-result',[
            'transaction' => $verifyResponse['data']['transaction'],
        ]);
    }
}
