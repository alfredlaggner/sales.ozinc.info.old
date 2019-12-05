<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('invoices', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('ext_id')->nullable()->index('ext_id');
			$table->string('name')->nullable();
			$table->string('type')->nullable();
			$table->string('sales_order')->nullable()->index('sales_order');
			$table->date('invoice_date')->nullable()->default('1900-01-01');
			$table->date('create_date')->nullable();
			$table->date('due_date')->nullable();
			$table->integer('age')->default(0);
			$table->float('amount_untaxed', 10)->nullable();
			$table->float('residual', 10)->nullable();
			$table->float('amount_untaxed_signed', 10)->nullable();
			$table->float('amount_tax', 10)->nullable();
			$table->float('amount_total', 10)->nullable();
			$table->integer('customer_id')->nullable();
			$table->string('customer_name')->nullable();
			$table->integer('sales_person_id');
			$table->string('sales_person_name')->nullable();
			$table->string('state')->nullable();
			$table->boolean('reconciled')->nullable()->default(0);
			$table->boolean('has_outstanding')->nullable();
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
		Schema::drop('invoices');
	}

}
