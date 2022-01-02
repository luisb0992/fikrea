<?php

/**
 * Modifica la tabla file_sharing_histories para solamente guardar el user_agent
 * de la conexión del usuario y extraer info que viene implicita como OS ...
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFieldUserAgentOnFileSharingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('file_sharing_histories', function (Blueprint $table) {
            $table->dropColumn("os");    // Elimino la columna os
            $table->dropColumn("device");// Elimino la columna device
            $table->text('user_agent')->change();       // User Agent
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
