<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->string('type', 192);
			$table->string('code', 192);
			$table->string('Type_barcode', 192);
			$table->string('name', 192);
            $table->string('account_holder', 192);
            $table->string('email', 192)->nullable();
            $table->string('email_password', 192)->nullable();
            $table->string('recovery_email', 192)->nullable();
            $table->string('account_email', 192)->nullable();
            $table->string('account_password', 192)->nullable();
            $table->string('passcode_pin', 192)->nullable();
            $table->string('number_company', 192)->nullable();
            $table->string('number_email_username', 192)->nullable();
            $table->string('number_password', 192)->nullable();
            $table->string('mobile_number', 192)->nullable();
            $table->string('proxy_website', 192)->nullable();
            $table->string('proxy_ip_host', 192)->nullable();
            $table->string('port', 192)->nullable();
            $table->string('proxy_username', 192)->nullable();
            $table->string('proxy_password', 192)->nullable();
			$table->float('cost', 10, 0);
			$table->float('price', 10, 0);
			$table->integer('category_id')->index('category_id');
			$table->integer('brand_id')->nullable()->index('brand_id_products');
			$table->integer('unit_id')->nullable()->index('unit_id_products');
			$table->integer('unit_sale_id')->nullable()->index('unit_id_sales');
			$table->integer('unit_purchase_id')->nullable()->index('unit_purchase_products');
			$table->float('TaxNet', 10, 0)->nullable()->default(0);
			$table->string('tax_method', 192)->nullable()->default('1');
			$table->text('image')->nullable();
            $table->string('existing_attatchment_id')->nullable();
			$table->text('note')->nullable();
			$table->float('stock_alert', 10, 0)->nullable()->default(0);
			$table->float('qty_min', 10, 0)->nullable()->default(0);
			$table->boolean('is_promo')->default(0);
			$table->float('promo_price', 10, 0)->default(0);
			$table->date('promo_start_date')->nullable();
			$table->date('promo_end_date')->nullable();
			$table->boolean('is_variant')->default(0);
			$table->boolean('is_imei')->default(0);
			$table->boolean('not_selling')->default(0);
			$table->boolean('is_active')->nullable()->default(1);
			$table->timestamps(6);
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('products');
	}

}
