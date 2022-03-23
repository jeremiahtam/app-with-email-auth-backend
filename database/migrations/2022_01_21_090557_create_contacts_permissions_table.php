<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContactsPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contacts_id');
            $table->boolean('last_seen');
            $table->boolean('live_location');
            $table->boolean('location_history');
            $table->boolean('removed');
            $table->timestamps();
            $table->foreign('contact_id')->references('id')->on('contacts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts_permissions');
    }
}
