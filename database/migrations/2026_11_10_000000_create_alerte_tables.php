<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quartiers', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
        });
        Schema::create('subscribers', function (Blueprint $t) {
            $t->id();
            $t->string('phone')->unique();
            $t->foreignId('quartier_id')->constrained();
            $t->string('channel')->default('sms');
            $t->string('token', 10)->nullable();
            $t->string('code', 6)->nullable();
            $t->string('telegram_chat_id')->nullable();
            $t->timestamp('verified_at')->nullable();
            $t->timestamps();
        });
        Schema::create('alerts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('quartier_id')->constrained();
            $t->string('type');
            $t->string('message', 140);
            $t->unsignedInteger('sent_count')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('quartiers');
    }
};
