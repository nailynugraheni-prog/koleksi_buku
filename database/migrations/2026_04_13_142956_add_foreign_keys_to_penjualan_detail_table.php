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
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->foreign(['id_barang'], 'fk_barang')->references(['id_barang'])->on('barang')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign(['id_penjualan'], 'fk_penjualan')->references(['id_penjualan'])->on('penjualan')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualan_detail', function (Blueprint $table) {
            $table->dropForeign('fk_barang');
            $table->dropForeign('fk_penjualan');
        });
    }
};
