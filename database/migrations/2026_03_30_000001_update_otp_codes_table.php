<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
            $table->string('code', 6)->after('user_id');
            $table->timestamp('expires_at')->after('code');
            $table->timestamp('used_at')->nullable()->after('expires_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'code', 'expires_at', 'used_at']);
        });
    }
};
