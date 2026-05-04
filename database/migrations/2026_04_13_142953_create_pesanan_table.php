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
        Schema::create('pesanan', function (Blueprint $table) {
            $table->increments('idpesanan');
            $table->string('nama');
            $table->timestamp('timestamp')->nullable()->useCurrent();
            $table->integer('total')->nullable()->default(0);
            $table->smallInteger('metode_bayar')->nullable();
            $table->smallInteger('status_bayar')->nullable()->default(0);
            $table->string('order_id_midtrans', 100)->nullable();
            $table->text('snap_token')->nullable();
            $table->string('transaction_status', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
