<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSaleinvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saleinvoices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ext_id')->nullable()->index('ext_id');
            $table->string('uid')->nullable();
            $table->string('invoice_number', 10)->nullable()->index('InvoiceNumber');
            $table->integer('order_id')->nullable()->index('customer_id');
            $table->integer('customer_id')->nullable();
            $table->integer('product_id')->nullable()->index('product_id');
            $table->text('name', 65535)->nullable();
            $table->integer('brand_id')->nullable();
            $table->string('brand')->nullable()->default('no brand');
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->date('order_date')->nullable()->index('order_date');
            $table->date('invoice_date')->nullable()->index('invoice_date');
            $table->date('write_date')->nullable();
            $table->char('code', 10)->nullable()->default('NULL');
            $table->float('amt_to_invoice', 10)->nullable();
            $table->float('amt_invoiced', 10)->nullable();
            $table->float('price_subtotal', 10)->nullable();
            $table->string('invoice_status')->nullable()->index('invoice_status');
            $table->boolean('is_paid')->default(0);
            $table->float('product_margin', 10)->nullable();
            $table->float('promotion_percent', 10)->nullable();
            $table->integer('qty_to_invoice')->nullable();
            $table->integer('qty_invoiced')->nullable();
            $table->integer('qty_delivered')->nullable();
            $table->integer('quantity')->nullable();
            $table->float('cost', 10)->nullable();
            $table->float('unit_price')->nullable();
            $table->float('margin', 10, 0)->nullable()->index('margin');
            $table->float('comm_percent', 8, 4)->nullable();
            $table->float('commission')->nullable();
            $table->integer('comm_version')->default(1);
            $table->string('comm_region', 1)->nullable();
            $table->integer('sales_person_id')->nullable();
            $table->float('odoo_margin', 10, 4)->nullable();
            $table->integer('quantity_corrected')->nullable();
            $table->string('ext_id_unit', 50)->nullable();
            $table->string('ext_id_shipping', 50)->nullable();
            $table->text('note', 65535)->nullable();
            $table->string('line_note_id')->nullable();
            $table->boolean('is_updated')->default(1);
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
        Schema::drop('saleinvoices');
    }
}
