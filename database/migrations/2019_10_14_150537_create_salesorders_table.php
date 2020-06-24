<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSalesordersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salesorders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ext_id')->nullable()->index('ext_id');
            $table->string('sales_order')->nullable();
            $table->string('invoice_status')->nullable();
            $table->integer('sales_order_id')->nullable();
            $table->date('create_date')->nullable();
            $table->date('order_date')->nullable();
            $table->date('write_date')->nullable();
            $table->float('amount_total')->nullable();
            $table->float('amount_tax')->nullable();
            $table->float('amount_untaxed')->nullable();
            $table->date('deliver_date')->nullable();
            $table->integer('salesperson_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->integer('complaint_id')->nullable();
            $table->integer('notes_id')->nullable();
            $table->boolean('is_manifest_created')->default(0);
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
        Schema::drop('salesorders');
    }
}
