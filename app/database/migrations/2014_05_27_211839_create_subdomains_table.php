<?php

use Illuminate\Database\Schema\Blueprint;

class CreateSubdomainsTable extends BaseMigration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subdomains', function(Blueprint $table)
		{
			$this
				->setTable($table)
				->addPrimary()
				->addString("db",64)
				->addString("username",16)
				->addString("password",50)
				->addForeign("user_id")
				->addTimestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('subdomains');
	}

}
