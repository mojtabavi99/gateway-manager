@php use App\Enums\Status; @endphp

@extends('layouts.main')

@section('page_title', 'جزئیات تراکنش #' . $transaction->id)

@section('content')
    <div class="container py-5">

        <div class="card shadow rounded-3 overflow-hidden">
            <div class="p-4 bg-light border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    @if($transaction->status == Status::SUCCESS)
                        <div class="fs-1 text-success"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold text-success">پرداخت موفق</h5>
                            <small class="text-muted">تراکنش شما با موفقیت انجام شد</small>
                        </div>
                    @else
                        <div class="fs-1 text-danger"><i class="bi bi-x-circle-fill"></i></div>
                        <div>
                            <h5 class="mb-0 fw-bold text-danger">پرداخت ناموفق</h5>
                            <small class="text-muted">تراکنش شما با خطا مواجه شد</small>
                        </div>
                    @endif
                </div>

                <div class="no-print d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm" id="copyAllBtn">
                        <i class="bi bi-clipboard"></i> کپی همه
                    </button>
                    <button class="btn btn-outline-primary btn-sm" id="printBtn">
                        <i class="bi bi-printer"></i> چاپ
                    </button>
                </div>
            </div>


            <div class="card-body bg-white p-4">

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <small class="text-muted">مبلغ تراکنش</small>
                            <h5 class="mt-2 mb-0">
                                <b>{{ number_format($transaction->amount) }}</b>
                                <span class="text-muted">تومان</span>
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <small class="text-muted">وضعیت پرداخت</small>
                            <h5 class="mt-2 mb-0">
                                <span class="{{ $transaction->status == Status::SUCCESS ? 'text-success' : 'text-danger' }}">
                                    <b>
                                        {{ $transaction->status == Status::SUCCESS ? 'موفق' : 'ناموفق' }}
                                    </b>
                                </span>
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <small class="text-muted">درگاه پرداخت</small>
                            <h5 class="mt-2 mb-0"><b>{{ $transaction->gateway->label() }}</b></h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <small class="text-muted">زمان تراکنش</small>
                            <h5 class="mt-2 mb-0">
                                <b>{{ Verta::instance($transaction->created_at)->format('j F Y - ساعت H:i') }}</b>
                            </h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <small class="text-muted">شناسه پرداخت (Payment ID)</small>
                            <h5 class="mt-2 mb-0"><b>{{ $transaction->payment_id }}</b></h5>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="p-3 border rounded-4 h-100">
                            <small class="text-muted">کدرهگیری</small>
                            <h5 class="mt-2 mb-0"><b>{{ $transaction->referral_code }}</b></h5>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-5 no-print">
                    <a href="/" class="btn btn-primary px-4 py-2">
                        <i class="bi bi-house"></i>
                        <b>بازگشت به صفحه اصلی</b>
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
