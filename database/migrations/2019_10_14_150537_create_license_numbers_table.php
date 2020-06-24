<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLicenseNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('license_numbers', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('odoo_id')->nullable();
            $table->integer('api_id')->nullable();
            $table->string('bcc_license')->nullable();
            $table->date('license_exp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('license_numbers');
    }
}
