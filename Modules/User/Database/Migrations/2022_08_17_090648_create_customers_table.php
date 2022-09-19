<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->integer('idReference');
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('doc_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->integer('status')->default(1);

            $table->string('localidad')->nullable();
            $table->string('latitud')->nullable();
            $table->string('longitud')->nullable();
            $table->string('is_vigia')->nullable();
            $table->string('category')->nullable();
            $table->string('potential_products')->nullable();
            $table->string('unit_quantity')->nullable();
            $table->string('result_of_the_visit')->nullable();
            $table->string('objective')->nullable();
            $table->string('next_visit_date')->nullable();

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
        Schema::dropIfExists('customers');
    }
}
