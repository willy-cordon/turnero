<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('appointment_id')->nullable();
            $table->bigInteger('supplier_id')->unsigned()->index()->nullable();
            $table->bigInteger('activity_instance_id')->nullable();
            $table->string('email_subject')->nullable();
            $table->boolean('status')->default(false)->nullable();
            $table->string('type')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
