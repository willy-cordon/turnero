<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulerCellLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduler_cell_locks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('lock_type')->nullable();
            $table->dateTime('lock_date')->nullable();
            $table->bigInteger('location_id')->unsigned()->index()->nullable();;
            $table->string('lock_key')->nullable();
            $table->string('hour')->nullable();
            $table->string('dock_name')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scheduler_cell_locks');
    }
}
