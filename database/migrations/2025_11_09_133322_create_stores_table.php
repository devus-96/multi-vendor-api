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
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');

            #$table->text('home_page_content');
            #$table->text('footer_content');
            $table->string('logo');
            $table->string('phone_number')->nullable();
            $table->email('email');
            $table->text('description');
            $table->integer('user_id')->unsigned();
            #$table->json('home_seo')->nullable();
            #$table->string('theme')->nullable();
            #$table->boolean('is_maintenance_on')->default(0);
            #$table->text('allowed_ips')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->softDeletes('deleted_at', precision: 0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
