<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEventImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // si existe la tabla se elimina primero
        Schema::dropIfExists('event_images');

        // luego se crea de nuevo
        Schema::create('event_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->text('url')->nullable();
            $table->timestamps();

        });

        // agregar atributo imagen MEDIUMBLOB
        DB::statement("ALTER TABLE event_images ADD image MEDIUMBLOB AFTER url");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_images');
    }
}
