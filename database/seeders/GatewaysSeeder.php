<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GatewaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_gateways')->insert([
            [
                'name' => 'پارسیان',
                'slug' => 'parsian',
                'merchant_code' => '27ctgjP1MkV5o8oISVbX',
                'terminal_id' => '45291339',
                'secret_key' => null,
                'username' => null,
                'password' => null,
                'logo' => '/storage/gateways/parsian.svg',
                'is_primary' => 1,
                'status' => 'active',
                'sort_order' => 1,
                'created_at' => Carbon::parse('2025-10-14 09:53:17'),
                'updated_at' => Carbon::parse('2025-10-14 09:53:17'),
            ],
            [
                'name' => 'سامان',
                'slug' => 'saman',
                'merchant_code' => '12609229',
                'terminal_id' => null,
                'secret_key' => null,
                'username' => null,
                'password' => null,
                'logo' => '/storage/gateways/saman.svg',
                'is_primary' => 0,
                'status' => 'active',
                'sort_order' => 2,
                'created_at' => Carbon::parse('2025-10-14 09:53:17'),
                'updated_at' => Carbon::parse('2025-10-14 09:53:17'),
            ],
            [
                'name' => 'سداد',
                'slug' => 'sadad',
                'merchant_code' => '140331215',
                'terminal_id' => '24051180',
                'secret_key' => '/plJc3KPW4iiFBvYY5MOc3UskZEtsfw9',
                'username' => null,
                'password' => null,
                'logo' => '/storage/gateways/sadad.svg',
                'is_primary' => 0,
                'status' => 'active',
                'sort_order' => 3,
                'created_at' => Carbon::parse('2025-10-14 09:53:17'),
                'updated_at' => Carbon::parse('2025-10-14 09:53:17'),
            ],
        ]);
    }
}
