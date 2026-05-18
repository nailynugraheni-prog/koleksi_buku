<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nomor_urut')->unique();
            $table->string('nomor_antrian', 20)->unique();
            $table->unsignedBigInteger('layanan_id');
            $table->string('nama', 150);
            $table->enum('status', ['waiting', 'called', 'skipped', 'done'])->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            $table->foreign('layanan_id')->references('id')->on('layanans')->restrictOnDelete();
            $table->index(['status', 'nomor_urut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrians');
    }
};