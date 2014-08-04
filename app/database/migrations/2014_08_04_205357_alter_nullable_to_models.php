<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterNullableToModels extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('models', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE models MODIFY COLUMN modelno VARCHAR(255) NULL DEFAULT NULL');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('models', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE models MODIFY COLUMN modelno VARCHAR(255) NOT NULL DEFAULT NULL');
		});
	}

}
