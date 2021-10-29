<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppointmentAppointmentUnloadTypeRelationship extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->bigInteger('unload_type_id')->unsigned()->index()->nullable();
            $table->foreign('unload_type_id')->references('id')->on('appointment_unload_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['unload_type_id']);
            $table->dropColumn('unload_type_id');
        });
    }
}
