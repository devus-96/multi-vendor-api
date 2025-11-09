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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role')->enum(['client', 'seller']);
            $table->string('password');
            $table->string('phone_number');
            $table->string('profile_photo_url')->nullable();
            $table->boolean('is_2fa_enabled')->default(false);
            $table->string('auth_provider')->nullable();
            $table->string("provider_id")->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('verified_at')->nullable(); // admin or system identity verification
            $table->rememberToken();
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');

            // 💡 La colonne last_activity doit être INTEGER par défaut (timestamp)
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
