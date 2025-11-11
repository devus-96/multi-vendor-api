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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('type');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->json('additional')->nullable();
            $table->timestamps();

            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('product_relations', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned();
            $table->integer('child_id')->unsigned();

            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('product_up_sells', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned();
            $table->integer('child_id')->unsigned();

            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('products')->onDelete('cascade');
        });

        Schema::create('product_cross_sells', function (Blueprint $table) {
            $table->integer('parent_id')->unsigned();
            $table->integer('child_id')->unsigned();

            $table->foreign('parent_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
