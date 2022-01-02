<?php

/**
 * Adiciona el campo 'device' en la tabla form_data
 * donde guardaremos el dispositivo con que se ha conectado el firmante
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceToFormDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('form_data', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'device' para adicionarla
            if (!Schema::hasColumn('form_data', 'device')) {
                $table->tinyInteger('device')
                    ->nullable()
                    ->comment('Dispositivo con que se ha conectado; Móvil, Tablet o PC');
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
        Schema::table('form_data', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'device' para eliminarla
            if (Schema::hasColumn('form_data', 'device')) {
                $table->dropColumn('device');
            }
        });
    }
}