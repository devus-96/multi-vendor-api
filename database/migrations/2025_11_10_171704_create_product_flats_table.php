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
        Schema::create('product_flats', function (Blueprint $table) {
            Schema::create('product_flat', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku');
            $table->string('type')->nullable();
            $table->string('product_number')->nullable(); //Numéro de produit alternatif (référence fabricant)
            $table->string('name')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('url_key')->nullable();
            $table->boolean('new')->nullable();
            $table->boolean('featured')->nullable();
            $table->boolean('status')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->decimal('price', 12, 4)->nullable();
            $table->decimal('special_price', 12, 4)->nullable();
            $table->date('special_price_from')->nullable();
            $table->date('special_price_to')->nullable();
            $table->decimal('weight', 12, 4)->nullable();
            $table->datetime('created_at')->nullable();
            $table->string('locale')->nullable();
            $table->string('channel')->nullable();
            #$table->integer('attribute_family_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned();
            $table->datetime('updated_at')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->boolean('visible_individually')->nullable();

            $table->unique(['product_id', 'channel', 'locale'], 'product_flat_unique_index');
            #$table->foreign('attribute_family_id')->references('id')->on('attribute_families')->onDelete('restrict');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('product_flat')->onDelete('cascade');
        });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_flats');
    }
};
