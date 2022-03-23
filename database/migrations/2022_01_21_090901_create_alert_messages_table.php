<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sender');
            $table->integer('receiver');
            $table->string('message');
            $table->string('location_name')->nullable();
            $table->decimal('latitude', 8, 6);
            $table->decimal('longitude', 9, 6);
            $table->boolean('email_sent')->default(0);
            $table->boolean('text_message_sent')->default(0);
            $table->boolean('removed')->default(0);
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
        Schema::dropIfExists('alert_messages');
    }
}
