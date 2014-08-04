<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNullableToAssets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('assets', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE assets MODIFY COLUMN serial VARCHAR(255) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN purchase_date DATE NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN purchase_cost DECIMAL(8, 2) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN order_number VARCHAR(255) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN notes TEXT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN status_id INT(11) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN warranty_months INT(3) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN supplier_id INT(11) NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN model INT(11) NULL DEFAULT NULL');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('assets', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE assets MODIFY COLUMN serial VARCHAR(255) NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN purchase_date DATE NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN purchase_cost DECIMAL(8, 2) NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN order_number VARCHAR(255) NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN notes TEXT NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN status_id INT(11) NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN warranty_months INT(3) NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN supplier_id INT(11) NOT NULL DEFAULT NULL');
            DB::statement('ALTER TABLE assets MODIFY COLUMN model_id INT(11) NOT NULL DEFAULT NULL');
		});
	}

}
