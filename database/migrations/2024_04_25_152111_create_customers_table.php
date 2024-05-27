<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('phone_number')->unique();
            $table->string('password');
            $table->string('email')->unique();
            $table->string('avatar')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->boolean('gender')->nullable();
            $table->string('address')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();

            $table->index('phone_number');
            $table->index('email');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
