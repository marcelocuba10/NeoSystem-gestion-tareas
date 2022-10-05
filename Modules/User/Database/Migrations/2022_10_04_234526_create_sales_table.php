<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->integer('seller_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('sale_date')->nullable();
            $table->string('order_date')->nullable();
            $table->string('type');
            $table->string('status');
            $table->decimal('total', 12, 0);
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
        Schema::dropIfExists('sales');
    }
}
