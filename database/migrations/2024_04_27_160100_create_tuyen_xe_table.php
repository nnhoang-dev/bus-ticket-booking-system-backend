<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('tuyen_xe', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('start_address');
            $table->foreign('start_address')
                ->references('id')
                ->on('nha_xe');

            $table->string('end_address');
            $table->foreign('end_address')
                ->references('id')
                ->on('nha_xe');
            $table->time('time');
            $table->unique(['start_address', 'end_address'], 'unique_tuyen_xe_route');
            $table->boolean('status')->default(1);
            $table->timestamps();

            Schema::index('tuyen_xe.end_address');
            Schema::index('tuyen_xe.start_address');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('tuyen_xe');
    }
};