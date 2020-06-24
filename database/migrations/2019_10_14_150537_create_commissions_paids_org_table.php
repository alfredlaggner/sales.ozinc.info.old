<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommissionsPaidsOrgTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions_paids_org', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ext_id')->nullable()->unique('ext_id');
            $table->integer('saved_commissions_id')->nullable();
            $table->boolean('is_paid')->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->string('paid_by')->nullable();
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
        Schema::drop('commissions_paids_org');
    }
}
