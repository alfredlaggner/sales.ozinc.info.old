<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceMonthlyTotalsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoice_monthly_totals', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('customer_name')->nullable();
			$table->string('oder_name')->nullable();
			$table->string('sales_person_name')->nullable();
			$table->float('total', 10)->nullable();
			$table->float('total_unsigned', 10)->nullable();
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
		Schema::drop('invoice_monthly_totals');
	}

}
