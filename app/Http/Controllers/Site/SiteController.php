<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\TransactionService;
use App\Traits\ExceptionTrait;
use Illuminate\Contracts\View\View;

class SiteController extends Controller
{
    use ExceptionTrait;

    protected TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function index(): View
    {
        $gateways = PaymentGateway::query()
            ->where('status', PaymentGateway::STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->get();

        return view('site.index', [
            'gateways' => $gateways,
        ]);
    }
}
