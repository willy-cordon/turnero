<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_instances', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('date')->nullable();
            $table->string('answer')->nullable();
            $table->bigInteger('activity_action_id')->unsigned()->index()->nullable();
            $table->string('status')->nullable();

            $table->bigInteger('appointment_id')->unsigned()->index()->nullable();
            $table->bigInteger('activity_id')->unsigned()->index()->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->foreign('activity_action_id')->references('id')->on('activity_actions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_instances');
    }
}
