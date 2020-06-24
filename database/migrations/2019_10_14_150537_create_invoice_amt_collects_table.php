<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceAmtCollectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_amt_collects', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('customer_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->bigInteger('week')->nullable();
            $table->string('customer_name')->nullable();
            $table->float('amount_to_collect', 10)->nullable();
            $table->float('amt_collected', 15)->nullable();
            $table->float('saved_residual', 15)->nullable();
            $table->string('note_by')->nullable();
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
        Schema::drop('invoice_amt_collects');
    }
}
