<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenNintyCommssion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ten_ninty_commssions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('month')->nullable();;
            $table->integer('year')->nullable();;
            $table->integer('rep_id')->nullable();;
            $table->double('goal','10','2')->nullable();;
            $table->double('volume','10','2')->nullable();;
            $table->double('commission','10','2')->nullable();;
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
        Schema::dropIfExists('ten_ninty_commssion');
    }
}
