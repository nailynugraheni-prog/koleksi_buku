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
        Schema::create('customer', function (Blueprint $table) {
            $table->bigIncrements('idcustomer');
            $table->string('nama', 100);
            $table->text('alamat');
            $table->string('provinsi', 100);
            $table->string('kota', 100);
            $table->string('kecamatan', 100);
            $table->string('kodepos_kelurahan', 50);
            $table->binary('foto_blob')->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer');
    }
};
