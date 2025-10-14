@extends('layouts.main')

@section('external_stylesheet')
    <link rel="stylesheet" href="{{ asset('assets/css/deposit-form.css') }}"/>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-12">
                <div class="card shadow rounded-3 p-4">
                    <div class="text-center mb-4">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="img-fluid" style="max-width: 120px;">
                    </div>

                    <hr class="mb-4">

                    <form method="POST" action="{{ route('deposit') }}">
                        @csrf

                        <p class="fw-bold mb-4">
                            لطفا برای شارژ حساب خود اطلاعات زیر را کامل کنید
                        </p>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label for="first_name" class="form-label">نام</label>
                                <input type="text" class="form-control text-center" id="first_name" name="first_name"
                                       value="{{ old('first_name') }}">
                                @error('first_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-6 mb-3">
                                <label for="last_name" class="form-label">نام خانوادگی</label>
                                <input type="text" class="form-control text-center" id="last_name" name="last_name"
                                       value="{{ old('last_name') }}">
                                @error('last_name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="mobile" class="form-label">شماره موبایل</label>
                                <input type="text" class="form-control text-center" id="mobile" name="mobile"
                                       value="{{ old('mobile') }}">
                                @error('mobile')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-12 mb-4">
                                <label for="amount" class="form-label">مبلغ (تومان)</label>
                                <input type="text" class="form-control text-center" id="amount" name="amount"
                                       value="{{ old('amount') }}" inputmode="numeric" autocomplete="off">
                                @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        @if($gateways && count($gateways) > 0)
                            <div class="mb-4">
                                <p class="fw-semibold mb-3">لطفا درگاه مورد نظر را انتخاب کنید</p>
                                <div class="d-flex flex-wrap justify-content-center gap-4">
                                    @foreach($gateways as $item)
                                        <label class="gateway-option">
                                            <input type="radio" name="gateway" value="{{ $item->gateway_no }}"
                                                @checked($item->is_primary)>
                                            <img src="{{ asset($item->logo) }}" alt="{{ $item->name }}">
                                            <span>{{ $item->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('gateway')
                                <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <button type="submit" class="btn btn-success fw-bold w-100 mt-3">
                            تایید و پرداخت
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@once
    @push('internal_scripts')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const amountInput = document.getElementById("amount");
                const fixDigits = str => str
                    .replace(/[۰-۹]/g, d => "۰۱۲۳۴۵۶۷۸۹".indexOf(d))
                    .replace(/[٠-٩]/g, d => "٠١٢٣٤٥٦٧٨٩".indexOf(d));

                amountInput.addEventListener("input", function(e) {
                    let value = fixDigits(e.target.value);
                    value = value.replace(/[^0-9]/g, "");
                    value = value.replace(/^0+/, "");
                    if (value) {
                        value = Number(value).toLocaleString("en-US");
                    }

                    e.target.value = value;
                });
                const form = amountInput.closest("form");
                form.addEventListener("submit", function() {
                    amountInput.value = fixDigits(amountInput.value.replace(/,/g, ""));
                });
            });
        </script>
    @endpush
@endonce
