<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penjualan_details', function (Blueprint $table) {
            $table->id('idpenjualan_detail');
            $table->unsignedBigInteger('id_penjualan');
            $table->string('id_barang', 8);
            $table->smallInteger('jumlah');
            $table->integer('subtotal');
            $table->timestamps();

            $table->foreign('id_penjualan')->references('id_penjualan')->on('penjualans')->cascadeOnDelete();
            $table->foreign('id_barang')->references('id_barang')->on('barangs')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualan_details');
    }
};