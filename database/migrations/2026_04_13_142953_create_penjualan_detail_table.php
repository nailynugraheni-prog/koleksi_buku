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
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->integer('idpenjualan_detail')->primary();
            $table->integer('id_penjualan')->index('idx_penjualandetail_penjualan');
            $table->string('id_barang', 8)->index('idx_penjualandetail_barang');
            $table->smallInteger('jumlah');
            $table->integer('subtotal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
    }
};
