<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('route_id');
            $table->foreign('route_id')
                ->references('id')
                ->on('routes');

            $table->string('bus_id');
            $table->foreign('bus_id')
                ->references('id')
                ->on('buses');

            $table->string('driver_id');
            $table->foreign('driver_id')
                ->references('id')
                ->on('employees');

            $table->integer('price');
            $table->string('seat')->default("");
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->index('route_id');
            $table->index('bus_id');
            $table->index('driver_id');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
