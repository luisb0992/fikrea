<?php

/**
 * Elimina el campo 'signer_id' en la tabla 'smses'
 *
 * Para adicionar los campos polimórficos _id, _type
 * con el objetivo de relacionar la tabla 'smses' con Firmantes y Contactos
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDeleteSignerIdToSmsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smses', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'signer_id' para eliminarla
            if (Schema::hasColumn('smses', 'signer_id')) {
                $table->dropColumn('signer_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smses', function (Blueprint $table) {
            // Crea la columna 'signer_id'
            $table->unsignedBigInteger('signer_id')
                ->comment('El firmante al que se envía el sms');        // El firmante
        });
    }
}
