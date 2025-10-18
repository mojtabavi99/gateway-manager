<?php

namespace App\Http\Controllers\Site;

use App\Exceptions\BaseException;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepositRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use App\Traits\ExceptionTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use RealRashid\SweetAlert\Facades\Alert;

class TransactionController extends Controller
{
    use ExceptionTrait;

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * @param DepositRequest $request
     * @return RedirectResponse
     * @throws BaseException
     */
    public function deposit(DepositRequest $request): RedirectResponse
    {
        $response = $this->transactionService->deposit($request->validated());
        if ($response['success']) {
            return redirect()->route('transaction.site.initiate_payment', $response['data']['id']);
        } else {
            Alert::toast($response['message'], 'error');
            return redirect()->back();
        }
    }

    /**
     * @param Transaction $transaction
     * @return RedirectResponse|Redirector
     */
    public function initiatePayment(Transaction $transaction): Redirector|RedirectResponse
    {
        $response = $this->transactionService->initiatePayment($transaction);
        if ($response['success']) {
            return redirect($response['data']['redirect_url']);
        }

        Alert::toast($response['message'], 'error');
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param Transaction $transaction
     * @return RedirectResponse
     * @throws BaseException
     */
    public function verifyPayment(Request $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->all();
        if (empty($data)) {
            $this->throwValidation('');
        }

        $data['amount'] = $transaction->amount;

        $response = $this->transactionService->verifyPayment($transaction, $data);

        return redirect()->route('transaction.site.payment_result', $response['data']['transaction']['id']);
    }

    /**
     * @param Transaction $transaction
     * @return View
     */
    public function paymentResult(Transaction $transaction): View
    {
        return view('site.transaction.payment-result', [
            'transaction' => $transaction,
        ]);
    }
}
