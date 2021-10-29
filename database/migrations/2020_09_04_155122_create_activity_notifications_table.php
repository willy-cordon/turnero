<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject')->nullable();
            $table->string('emails_to')->nullable();
            $table->string('emails_cc')->nullable();
            $table->bigInteger('activity_action_id')->unsigned()->index()->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('activity_notifications');
    }
}
