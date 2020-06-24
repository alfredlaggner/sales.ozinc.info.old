<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePromotionToSaleinvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_to_saleinvoices', function (Blueprint $table) {
            $table->integer('promotion_id')->nullable()->index('promotion');
            $table->integer('saleinvoice_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('promotion_to_saleinvoices');
    }
}
