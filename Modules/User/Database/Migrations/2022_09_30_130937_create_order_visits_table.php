<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id');
            $table->integer('product_id');
            $table->decimal('price', 12, 0);
            $table->integer('quantity');
            $table->decimal('amount', 12, 0);
            $table->timestamps();

            $table->foreign('visit_id')
                ->references('id')->on('customer_visits')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_visits');
    }
}
