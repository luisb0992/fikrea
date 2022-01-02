<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('token')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('type')->nullable();
            $table->smallInteger('event_status')->nullable();
            $table->unsignedBigInteger('purpose_event_id')->nullable();
            $table->unsignedBigInteger('min_goal')->nullable();
            $table->unsignedBigInteger('max_goal')->nullable();
            $table->boolean('is_public')->nullable()->default(false);
            $table->boolean('is_anonymous')->nullable()->default(false);
            $table->boolean('kiosk_mode')->nullable()->default(false);
            $table->boolean('is_block_return')->nullable()->default(false);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });

        // agregar atributo imagen MEDIUMBLOB
        DB::statement("ALTER TABLE events ADD image MEDIUMBLOB AFTER description");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
