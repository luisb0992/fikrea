<?php

/**
 * Adiciona el campo campo 'duration' en la tabla screens
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDurationToScreensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('screens', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'duration' para adicionarla
            if (!Schema::hasColumn('screens', 'duration')) {
                $table->string('duration')
                    ->comment("La duración del video")
                    ->after('saved');
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
        Schema::table('screens', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'duration' para eliminarla
            if (Schema::hasColumn('screens', 'duration')) {
                $table->dropColumn('duration');
            }
        });
    }
}
