<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppointmentAppointmentTypeRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->bigInteger('type_id')->unsigned()->index()->nullable();
            $table->foreign('type_id')->references('id')->on('appointment_types');
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
           // $table->dropForeign(['type_id']);
           // $table->dropColumn('type_id');
        });
    }
}
