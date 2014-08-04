<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterStateCountryOfLocations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        DB::statement('ALTER TABLE locations MODIFY COLUMN state VARCHAR(2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE locations MODIFY COLUMN country VARCHAR(2) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE locations MODIFY COLUMN city VARCHAR(255) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE locations MODIFY COLUMN address VARCHAR(80) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE locations MODIFY COLUMN address2 VARCHAR(80) NULL DEFAULT NULL');
        DB::statement('ALTER TABLE locations MODIFY COLUMN zip VARCHAR(10) NULL DEFAULT NULL');
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('locations', function(Blueprint $table)
		{
            DB::statement('ALTER TABLE locations MODIFY COLUMN state VARCHAR(2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE locations MODIFY COLUMN country VARCHAR(2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE locations MODIFY COLUMN city VARCHAR(255) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE locations MODIFY COLUMN address VARCHAR(80) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE locations MODIFY COLUMN address2 VARCHAR(80) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE locations MODIFY COLUMN zip VARCHAR(10) NOT NULL DEFAULT 0');
		});
	}

}
