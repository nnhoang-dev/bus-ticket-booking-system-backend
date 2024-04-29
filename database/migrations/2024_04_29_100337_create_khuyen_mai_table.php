<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('khuyen_mai', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->integer('discount');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('khuyen_mai');
    }
};
