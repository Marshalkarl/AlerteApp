<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $t) {
            $t->string('login_code', 64)->nullable();
            $t->timestamp('login_code_expires_at')->nullable();
            $t->unsignedTinyInteger('login_attempts')->default(0);
            $t->timestamp('last_login_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $t) {
            $t->dropColumn(['login_code', 'login_code_expires_at', 'login_attempts', 'last_login_at']);
        });
    }
};
