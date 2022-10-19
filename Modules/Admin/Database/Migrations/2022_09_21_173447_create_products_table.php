<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('code')->nullable();
            $table->string('custom_code')->nullable();
            $table->integer('idReference')->nullable();
            $table->integer('category_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->decimal('sale_price', 12, 0);
            $table->decimal('purchase_price', 12, 0)->nullable();
            $table->integer('inventory');
            $table->string('supplier')->nullable();
            $table->string('phone_supplier')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
