<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNewtableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('newtable', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('order_number')->nullable();
            $table->date('order_date')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('rep')->nullable();
            $table->string('state')->nullable();
            $table->string('sku')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('name')->nullable();
            $table->string('product_category')->nullable();
            $table->string('product_subcategory')->nullable();
            $table->integer('qty_invoiced')->nullable();
            $table->integer('qty_delivered')->nullable();
            $table->float('amount_to_invoice', 10)->nullable();
            $table->float('amount_invoiced', 10)->nullable();
            $table->float('unit_price', 10)->nullable();
            $table->float('cost', 10)->nullable();
            $table->float('commission', 10)->nullable();
            $table->float('comm_percent', 10)->nullable();
            $table->float('amount_tax', 10)->nullable();
            $table->float('amount_total', 10)->nullable();
            $table->float('amount_untaxed', 10)->nullable();
            $table->string('comm_region', 1)->nullable();
            $table->integer('ext_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->date('invoice_date')->nullable();
            $table->integer('product_id')->nullable();
            $table->integer('brand_id')->nullable();
            $table->float('margin', 10)->nullable();
            $table->float('amount', 10)->nullable();
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
        Schema::drop('newtable');
    }
}
