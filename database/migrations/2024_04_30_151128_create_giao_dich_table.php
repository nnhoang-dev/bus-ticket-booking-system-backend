<?php

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
        Schema::create('giao_dich', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('vnp_Amount');
            $table->string('vnp_BankCode');
            $table->string('vnp_CardType');
            $table->string('vnp_OrderInfo');
            $table->string('vnp_PayDate');
            $table->string('vnp_ResponseCode');
            $table->string('vnp_TmnCode');
            $table->string('vnp_TransactionNo');
            $table->string('vnp_TransactionStatus');
            $table->string('vnp_TxnRef');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('giao_dich');
    }
};
