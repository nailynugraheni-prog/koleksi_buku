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
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('barcode'); // Kolom barcode untuk relasi
            
            // Definisi Foreign Key ke tabel stores
            $table->foreign('barcode')->references('barcode')->on('stores')->onDelete('cascade');
            
            $table->unsignedBigInteger('user_id')->nullable();
            $table->double('sales_latitude');
            $table->double('sales_longitude');
            $table->double('sales_accuracy');
            $table->double('jarak');
            $table->double('threshold_efektif');
            $table->enum('status', ['DITERIMA', 'DITOLAK']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
