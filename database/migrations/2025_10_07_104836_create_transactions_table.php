<?php

use App\Enums\Gateway;
use App\Enums\Status;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonded_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('gateway', Gateway::values())->default(Gateway::SADAD);
            $table->enum('payment_method', ['online', 'cash', 'installment'])->default('online');
            $table->string('payment_id')->nullable();
            $table->string('referral_code')->nullable();
            $table->unsignedBigInteger('amount');
            $table->enum('status', Status::paymentValues())->default(Status::PENDING);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
