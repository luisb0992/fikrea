<?php

/**
 * Modifica la tabla signer_visits para eliminar las columnas os y device
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropFieldsOnSignerVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signer_visits', function (Blueprint $table) {
            $table->dropColumn('os');       // Elimino la columna os
            $table->dropColumn('device');   // Elimino la columna os
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
