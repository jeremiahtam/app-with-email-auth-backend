<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_updates', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->unsignedInteger('location_history_id');
            $table->decimal('latitude', 8, 6);
            $table->decimal('longitude', 9, 6);
            $table->string('location_name')->nullable();
            $table->boolean('removed')->default(0);
            $table->timestamps();
            $table->foreign('location_history_id')->references('id')->on('location_histories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_updates');
    }
}
