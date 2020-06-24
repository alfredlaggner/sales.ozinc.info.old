<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInvoiceNotesBackupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_notes_backup', function (Blueprint $table) {
            $table->bigInteger('id', true)->unsigned();
            $table->integer('invoice_id')->nullable();
            $table->string('sales_order')->nullable();
            $table->text('note', 65535)->nullable();
            $table->float('amount_to_collect', 10)->nullable();
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
        Schema::drop('invoice_notes_backup');
    }
}
