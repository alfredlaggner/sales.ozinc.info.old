<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersManifestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers_manifest', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ext_id')->nullable()->index('customer_id');
            $table->string('ext_id_contact')->nullable()->index('customers_ext_id_contact_index');
            $table->string('license', 50)->nullable();
            $table->string('license2')->nullable();
            $table->boolean('tax_free')->default(0);
            $table->string('name')->nullable();
            $table->string('street')->nullable();
            $table->string('street2')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->string('phone', 50)->nullable();
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
        Schema::drop('customers_manifest');
    }
}
