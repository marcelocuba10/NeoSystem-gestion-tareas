<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemOrderVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_order_visits', function (Blueprint $table) {
            $table->id();
            $table->integer('visit_id');
            $table->integer('product_id');
            $table->decimal('price', 12, 0);
            $table->integer('quantity');
            $table->decimal('amount', 12, 0);
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
        Schema::dropIfExists('item_order_visits');
    }
}
