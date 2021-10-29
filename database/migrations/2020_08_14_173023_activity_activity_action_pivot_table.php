<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ActivityActivityActionPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_activity_action', function (Blueprint $table){


        $table->bigIncrements('id');
        $table->bigInteger('activity_id')->unsigned()->index();
        $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
        $table->bigInteger('activity_action_id')->unsigned()->index();
        $table->foreign('activity_action_id')->references('id')->on('activity_actions')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_activity_action');
    }
}
