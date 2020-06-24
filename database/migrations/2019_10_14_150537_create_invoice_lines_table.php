<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceLinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->nullable();
            $table->string('order_number', 10)->nullable()->index('InvoiceNumber');
            $table->string('invoice_number')->nullable();
            $table->string('invoice_name')->nullable();
            $table->integer('order_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('sales_person_id')->nullable();
            $table->integer('ext_id')->nullable()->index('ext_id');
            $table->date('invoice_date')->nullable()->comment('compatibility only');
            $table->string('invoice_state')->nullable();
            $table->date('order_date')->nullable();
            $table->date('create_date')->nullable();
            $table->char('code', 10)->nullable()->default('NULL');
            $table->text('name', 65535)->nullable();
            $table->integer('quantity')->nullable();
            $table->float('qty_invoiced', 10)->nullable();
            $table->float('unit_price')->nullable();
            $table->float('price_total', 10)->nullable();
            $table->float('amt_invoiced', 10)->nullable();
            $table->float('price_subtotal', 10)->nullable();
            $table->string('ext_id_unit', 50)->nullable();
            $table->float('cost', 10)->nullable();
            $table->float('margin', 10, 0)->nullable();
            $table->float('product_margin', 10)->nullable();
            $table->integer('comm_version')->default(1);
            $table->string('comm_region', 1)->nullable();
            $table->float('comm_percent', 8, 4)->nullable();
            $table->float('commission')->nullable();
            $table->float('promotion_percent', 10)->nullable();
            $table->float('amt_to_invoice', 10)->nullable()->default(0.00)->comment('compatibility only ');
            $table->integer('qty_to_invoice')->nullable()->default(0);
            $table->integer('qty_delivered')->nullable();
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
        Schema::drop('invoice_lines');
    }
}
