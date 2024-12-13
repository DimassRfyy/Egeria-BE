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
        Schema::create('booking_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('trx_id');
            $table->string('proof');
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('total_tax_amount');
            $table->boolean('is_paid');
            $table->text('address');
            $table->string('city');
            $table->string('post_code');
            $table->unsignedBigInteger('sub_total_amount');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_transactions');
    }
};
