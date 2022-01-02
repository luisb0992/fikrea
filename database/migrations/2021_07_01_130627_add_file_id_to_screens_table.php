<?php

/**
 * Adiciona el campo 'file_id' en la tabla screens
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFileIdToScreensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('screens', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'file_id' para adicionarla
            if (!Schema::hasColumn('screens', 'file_id')) {
                $table->string('file_id')
                    ->nullable()
                    ->comment("El archivo generado a partir de la grabación")
                    ->after('duration');
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
            // Verifico que la tabla tenga una columna 'file_id' para eliminarla
            if (Schema::hasColumn('screens', 'file_id')) {
                $table->dropColumn('file_id');
            }
        });
    }
}
