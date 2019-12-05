<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStockPickingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stock_pickings', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('ext_id')->nullable();
			$table->string('salesorder_number')->nullable();
			$table->date('date')->nullable();
			$table->string('name');
			$table->string('state')->nullable();
			$table->integer('product_id')->nullable();
			$table->string('product_name')->nullable();
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
		Schema::drop('stock_pickings');
	}

}
