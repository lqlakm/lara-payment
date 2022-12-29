<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 100);
            $table->string('payment_id', 100);
            $table->string('payer_email');
            $table->float('amount', 11, 2);
            $table->string('currency', 10);
            $table->string('payment_status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};