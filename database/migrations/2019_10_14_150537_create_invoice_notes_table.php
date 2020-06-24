<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_notes', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->integer('customer_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('invoice_id')->nullable();
            $table->text('note', 65535)->nullable();
            $table->char('note_by')->nullable();
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
        Schema::drop('invoice_notes');
    }
}
