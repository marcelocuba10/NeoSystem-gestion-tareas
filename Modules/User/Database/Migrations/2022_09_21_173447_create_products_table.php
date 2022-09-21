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
            $table->string('name');
            $table->integer('idReference');
            $table->string('description')->nullable();
            $table->double('purchase_price', 8, 3);
            $table->double('sale_price', 8, 3);
            $table->string('img_product')->nullable();
            $table->integer('quantity');
            $table->string('supplier')->nullable();
            $table->string('phone_supplier')->nullable();
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
