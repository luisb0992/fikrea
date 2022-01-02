<?php

/**
 * Modifica la columna 'type' en la tabla 'verification_form_sharings'
 * se le cambia el valor por defecto a '0' que es tipo de envío automático
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumnToVerificationFormSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verification_form_sharings', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'longitud' para modificarla
            if (Schema::hasColumn('verification_form_sharings', 'type')) {
                $table->integer('type')
                    ->default(0)
                    ->comment('Tipo de envío de correo recordatorio')
                    ->change();
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
        Schema::table('verification_form_sharings', function (Blueprint $table) {
            //
        });
    }
}
