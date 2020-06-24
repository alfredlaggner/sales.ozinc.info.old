<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStockMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_moves', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ext_id')->nullable()->unique('ext_id');
            $table->string('sku')->nullable();
            $table->integer('picking_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('name')->nullable();
            $table->date('date')->nullable();
            $table->string('lot_name')->nullable();
            $table->string('reference')->nullable();
            $table->string('location')->nullable();
            $table->string('location_dest')->nullable();
            $table->integer('qty_done')->nullable()->default(0);
            $table->string('state')->nullable();
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
        Schema::drop('stock_moves');
    }
}
