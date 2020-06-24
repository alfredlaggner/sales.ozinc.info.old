<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromotionSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_skus', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('promotion_id')->nullable();
            $table->integer('margin_id')->nullable();
            $table->timestamps();
            $table->unique(['promotion_id', 'margin_id'], 'promotion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('promotion_skus');
    }
}
