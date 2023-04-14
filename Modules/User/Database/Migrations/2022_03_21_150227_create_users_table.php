<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('idReference');
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('address')->nullable();
            $table->string('email')->unique();
            $table->string('doc_id')->unique();
            $table->integer('main_user')->default(0);
            $table->string('seller_contact_1')->nullable();
            $table->string('seller_contact_2')->nullable();
            $table->string('city')->nullable();
            $table->string('estate')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('img_profile')->nullable();
            $table->integer('meta_visits')->nullable();
            $table->integer('count_meta_visits')->nullable();
            $table->decimal('meta_billing', 12, 0)->nullable();
            $table->decimal('count_meta_billing', 12, 0)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        DB::statement("ALTER TABLE users AUTO_INCREMENT = 100;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
