<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAgedReceivablesTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aged_receivables_totals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rep_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->char('customer')->nullable();
            $table->char('rep')->nullable();
            $table->float('range0', 15)->nullable();
            $table->float('range1', 15)->nullable();
            $table->float('range2', 15)->nullable();
            $table->float('range3', 15)->nullable();
            $table->float('range4', 15)->nullable();
            $table->float('range5', 15)->nullable();
            $table->float('range6', 15)->nullable();
            $table->float('range7', 15)->nullable();
            $table->float('range8', 15)->nullable();
            $table->float('residual', 15)->nullable();
            $table->float('customer_total', 15)->nullable();
            $table->float('to_collect', 15)->nullable();
            $table->float('collected', 15)->nullable();
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
        Schema::drop('aged_receivables_totals');
    }
}
