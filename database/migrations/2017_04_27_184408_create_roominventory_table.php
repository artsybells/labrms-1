<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoominventoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('roominventory', function(Blueprint $table)
		{
			$table->integer('room_id')->unsigned();
			$table->foreign('room_id')->references('id')->on('room')
										->onUpdate('cascade')
										->onDelete('cascade');
			$table->integer('item_id')->primary()->unsigned();
			$table->foreign('item_id')->references('id')->on('itemprofile')
										->onUpdate('cascade')
										->onDelete('cascade');
			$table->timestamps();
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
		Schema::drop('roominventory');
	}

}
