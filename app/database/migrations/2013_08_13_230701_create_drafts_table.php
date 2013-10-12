<?php

use Illuminate\Database\Migrations\Migration;

class CreateDraftsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('drafts',function($t){
			$t->increments('id');
			$t->biginteger('user_id');
			$t->biginteger('draft_id');
			$t->string('title',200);
			$t->text('memo');
			$t->integer('status')->default(0);
			$t->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('drafts');
	}

}