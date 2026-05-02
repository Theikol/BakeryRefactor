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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->string('order_number', 50)->unique()->nullable();
        $table->string('customer_name', 100);
        $table->string('customer_email', 100);
        $table->string('customer_phone', 20);
        $table->text('customer_address');
        $table->text('notes')->nullable();
        $table->string('payment_method', 50)->nullable();
        $table->integer('total_price');
        $table->string('status', 50)->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
