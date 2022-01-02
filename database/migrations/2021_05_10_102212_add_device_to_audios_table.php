<?php

/**
 * Adiciona el campo 'device' en la tabla audios
 * donde guardaremos el dispositivo con que se ha conectado el firmante
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceToAudiosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audios', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'device' para adicionarla
            if (!Schema::hasColumn('audios', 'device')) {
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
        Schema::table('audios', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'device' para eliminarla
            if (Schema::hasColumn('audios', 'device')) {
                $table->dropColumn('device');
            }
        });
    }
}
