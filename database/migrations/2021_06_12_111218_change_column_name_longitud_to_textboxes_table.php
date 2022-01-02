<?php

/**
 * Modifica la columna 'longitud' en la tabla textboxes
 * se le cambia el nombre por longitud
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNameLongitudToTextboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('textboxes', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'longitud' para modificarla
            if (Schema::hasColumn('textboxes', 'longitud')) {
                $table->renameColumn('longitud', 'longitude');
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
        Schema::table('textboxes', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'longitude' para modificarla
            if (Schema::hasColumn('textboxes', 'longitude')) {
                $table->renameColumn('longitude', 'longitud');
            }
        });
    }
}
