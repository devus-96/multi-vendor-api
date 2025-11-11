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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('position')->default(0);
            $table->string('image')->nullable();
            $table->string('category_banner')->nullable();
            $table->boolean('status')->default(0);
            $table->string('display_mode')->default('products_and_description')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->text('additional')->nullable();

            // Relation auto-référente
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
