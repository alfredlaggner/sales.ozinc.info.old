<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoicesPaid20190715112152Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices_paid_2019_07_15_11_21_52', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ext_id')->nullable()->index('ext_id');
            $table->char('month')->nullable();
            $table->date('order_date')->nullable();
            $table->date('invoice_date')->nullable();
            $table->char('order_number')->nullable();
            $table->char('name')->nullable();
            $table->char('customer_name')->nullable();
            $table->char('rep')->nullable();
            $table->char('sku')->nullable();
            $table->char('brand_name')->nullable();
            $table->char('category')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('sales_person_id')->nullable();
            $table->float('cost')->nullable();
            $table->float('unit_price')->nullable();
            $table->float('commission_percent', 8, 4)->nullable();
            $table->float('commission')->nullable();
            $table->float('amount')->nullable();
            $table->float('amount_tax')->nullable();
            $table->float('amount_untaxed')->nullable();
            $table->float('amount_total')->nullable();
            $table->float('margin')->nullable();
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
        Schema::drop('invoices_paid_2019_07_15_11_21_52');
    }
}
