<?php

/**
 * Crea la tabla 'screens'
 *
 * La misma se utilizará para guardar los archivos de grabación de pantalla
 * temporales que el usuario vaya grabando al finalizar cada video,
 * con el objetivo de minimizar el costo en tiempo de ejecución
 * de la subida de todo el volumen de datos que se debe enviar al servidor
 * al finalizar todo el proceso, donde se enviarían solo los datos que no se 
 * hayan enviado hasta ese momento.
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScreensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('user_id')->comment('Usuario que sube el archivo');
            $table->text('filename')->comment('Nombre del archivo');
            $table->string('type')->comment('Tipo del archivo');
            $table->string('size')->comment('Tamaño del archivo');
            $table->string('duration')->comment('Tiempo de duración del video');
            $table->integer('path')->nullable()->comment('Carpeta padre de ubicación del archivo');
            $table->string('token')->nullable()->comment('Token único del archivo');
            $table->boolean('saved')->default(0)->comment('Si se ha guardado como un archivo en files');

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
        Schema::dropIfExists('screens');
    }
}
