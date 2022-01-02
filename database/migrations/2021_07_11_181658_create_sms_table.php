<?php

/**
 * Crea la tabla 'smses' para el control de los sms que se envian a los firmantes
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smses', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('signer_id')
                ->comment('El firmante al que se envía el sms');        // El firmante
            
            $table->datetime('sended_at')
                ->comment('El momento en que se envía el sms o nulo')
                ->nullable();                                           // Fecha en que se envía

            $table->datetime('created_at')
                ->comment('El momento en que se registra el envío')
                ->useCurrent();                                         // Fecha en que guarda el registro
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smses');
    }
}
