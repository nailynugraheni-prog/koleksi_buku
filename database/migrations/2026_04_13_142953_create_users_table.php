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
        Schema::create('users', function (Blueprint $table) {
            $table->integer('iduser')->primary();
            $table->string('username', 45)->nullable();
            $table->string('password', 100)->nullable();
            $table->integer('idrole')->nullable();
            $table->string('email')->nullable();
            $table->string('id_google', 256)->nullable();
            $table->string('otp', 6)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
