<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_visits', function (Blueprint $table) {
            $table->id();
            $table->integer('visit_number');
            $table->integer('customer_id');
            $table->integer('seller_id');
            $table->string('visit_date');
            $table->string('next_visit_date')->nullable();
            $table->string('next_visit_hour')->nullable();
            $table->string('result_of_the_visit')->nullable();
            $table->string('objective')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('customer_visits');
    }
}
