<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{

    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->integer('idReference');
            $table->integer('customer_id');
            $table->unsignedBigInteger('visit_id')->nullable();
            $table->string('date');
            $table->string('hour');
            $table->string('action');
            $table->string('status');
            $table->string('observation', 500)->nullable();
            $table->timestamps();

            $table->foreign('visit_id')
                ->references('id')->on('customer_visits')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
