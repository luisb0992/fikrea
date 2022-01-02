<?php

/**
 * Modifica la tabla signer_visits
 * - Se adicionan los campos os y device para registrar 
     el sistema operativo y el dispositivo del usuario

 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToSignerVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signer_visits', function (Blueprint $table) {
            $table->string('os')->nullable();       // Sistema operativo
            $table->string('device')->nullable();   // Dispositivo
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signer_visits', function (Blueprint $table) {
            //
        });
    }
}
