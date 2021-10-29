<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentChangeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_change_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('field_name')->nullable();
            $table->string('field_value_text')->nullable();
            $table->string('field_value_text_old')->nullable();
            $table->bigInteger('appointment_id')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_change_logs');
    }
}
