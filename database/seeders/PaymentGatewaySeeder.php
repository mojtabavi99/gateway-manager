<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_gateways')->insert([
            'name' => 'پارسیان',
            'slug' => 'parsian',
            'merchant_code' => '27ctgjP1MkV5o8oISVbX',
        ]);

        DB::table('payment_gateways')->insert([
            'name' => 'سداد',
            'slug' => 'sadad',
            'merchant_code' => '27ctgjP1MkV5o8oISVbX',
        ]);

        DB::table('payment_gateways')->insert([
            'name' => 'سامان',
            'slug' => 'saman',
            'merchant_code' => '27ctgjP1MkV5o8oISVbX',
        ]);
    }
}
