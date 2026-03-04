<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users','otp_hashed')) {
                $table->string('otp_hashed')->nullable()->after('otp');
            }
            if (! Schema::hasColumn('users','otp_sent_at')) {
                $table->timestamp('otp_sent_at')->nullable()->after('otp_hashed');
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_hashed','otp_sent_at']);
        });
    }
};