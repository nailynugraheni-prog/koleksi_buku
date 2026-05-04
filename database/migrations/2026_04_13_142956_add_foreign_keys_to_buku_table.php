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
        Schema::table('buku', function (Blueprint $table) {
            $table->foreign(['idkategori'], 'fk_buku_kategori')->references(['idkategori'])->on('kategori')->onUpdate('no action')->onDelete('restrict');
            $table->foreign(['created_by'], 'fk_buku_user_created_by')->references(['iduser'])->on('users')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buku', function (Blueprint $table) {
            $table->dropForeign('fk_buku_kategori');
            $table->dropForeign('fk_buku_user_created_by');
        });
    }
};
