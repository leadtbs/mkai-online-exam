<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNcChoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nc_choices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('choice_set_id');
            $table->foreign('choice_set_id')->references('id')->on('nc_choice_set')->onDelete('cascade');
            $table->string('choices')->nullable();
            $table->integer('correct')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nc_choices');
    }
}
