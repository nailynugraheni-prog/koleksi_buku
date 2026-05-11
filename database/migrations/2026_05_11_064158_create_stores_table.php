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
        Schema::create('stores', function (Blueprint $table) {
            // Taruh di sini, $table->id() dihapus karena 'barcode' jadi primary key
            $table->string('barcode')->primary(); 
            $table->string('nama_toko');
            $table->double('latitude');
            $table->double('longitude');
            $table->double('accuracy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
