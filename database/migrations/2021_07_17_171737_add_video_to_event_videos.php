<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVideoToEventVideos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('event_videos', function (Blueprint $table) {

            $table->dropColumn('path');
            $table->dropColumn('type');
            $table->dropColumn('size');
            $table->dropColumn('duration');

            // agregar atributo imagen MEDIUMBLOB
            DB::statement("ALTER TABLE event_videos ADD video MEDIUMBLOB AFTER url");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_videos', function (Blueprint $table) {
            $table->text('path')->nullable();
            $table->text('type')->nullable();
            $table->string('size')->nullable();
            $table->string('duration')->nullable();
        });
    }
}
