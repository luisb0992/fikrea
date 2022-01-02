<?php

/**
 * Se agrega un campo token en la tabla contacts del usuario
 * para identificarlo al pulsar aceder al sistema sobre un enlace
 * que se ha enviado a varios contactos
 * Compartición de archivos a un destinatario por ejemplo.
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldTokenToContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            
            $table->string('token');    // Token para identificar el contacto

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
}
