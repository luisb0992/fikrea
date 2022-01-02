<?php

/**
 * Tabla para guardar el histórico de visitas sobre un archivo compartido por 
 * un usuario
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileSharingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_sharing_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('file_sharing_id');  // Id de la compartición de archivo
            $table->unsignedBigInteger('user_id');          // Id del usuario que comparte el archivo

            $table->string('ip')->nullable();               // IP
            $table->string('user_agent')->nullable();       // Navegador
            $table->string('os')->nullable();               // Sistema Operativo
            $table->string('device')->nullable();           // Dispositivo
            $table->double('latitude')->nullable();         // Latitud
            $table->double('longitude')->nullable();        // Longitud
            $table->timestamp('starts_at')->useCurrent();   // Inicio de la visita
            $table->timestamp('ends_at')->nullable();       // Fin de la visita

            $table->string('contact_token')->nullable();    // Token del destinatario de la compartición de archivo

            $table->longText('downloaded_at')->nullable();  // Array de fechas en las que ha sido 
                                                            // descargado el archivo

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
        Schema::dropIfExists('file_sharing_histories');
    }
}
