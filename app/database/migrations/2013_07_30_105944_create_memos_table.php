<?php

use Illuminate\Database\Migrations\Migration;

class CreateMemosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('memos',function($table){
            $table->increments('id');
            $table->biginteger('user_id');
            $table->string('title',200);
            $table->text('memo');
            $table->string('urlpath',20);
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('memos');
		$table->drop_foreign('memos_user_id_foreign');
	}

}
