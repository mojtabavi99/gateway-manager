<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Driver
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default payment driver that will be used
    | by the application. You may change it via environment variable.
    |
    */
    'default' => env('DEFAULT_PAYMENT_DRIVER', 'parsian'),

    /*
    |--------------------------------------------------------------------------
    | Parsian Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | WSDL URLs for Parsian payment gateway.
    |
    */
    'parsian' => [
        'init_wsdl' => 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL',
        'verify_wsdl' => 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sadad Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | WSDL URLs for Sadad payment gateway.
    |
    */
    'sadad' => [
        'init_wsdl' => 'https://pec.shaparak.ir/NewIPGServices/Sale/SaleService.asmx?WSDL',
        'verify_wsdl' => 'https://pec.shaparak.ir/NewIPGServices/Confirm/ConfirmService.asmx?WSDL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Saman Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | WSDL URLs for Saman payment gateway.
    |
    */
    'saman' => [
        'init_wsdl' => 'https://sep.shaparak.ir/Payments/InitPayment.asmx?WSDL',
        'verify_wsdl' => 'https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Zarinpal Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | WSDL URLs for Zarinpal payment gateway.
    |
    */
    'zarinpal' => [
        'sandbox_wsdl' => 'https://sandbox.zarinpal.com/pg/services/WebGate/wsdl',
        'production_wsdl' => 'https://www.zarinpal.com/pg/services/WebGate/wsdl',
    ],
];
