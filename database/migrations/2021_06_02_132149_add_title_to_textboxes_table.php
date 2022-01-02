<?php

/**
 * Adiciona el campo 'title' en la tabla textboxes
 * donde guardaremos el título de la caja de texto del firmante externo
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleToTextboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('textboxes', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'title' para adicionarla
            if (!Schema::hasColumn('textboxes', 'title')) {
                $table->string('title')
                    ->nullable()
                    ->comment('Título de la caja de texto del firmante externo');
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
            // Verifico que la tabla tenga una columna 'title' para eliminarla
            if (Schema::hasColumn('textboxes', 'title')) {
                $table->dropColumn('title');
            }
        });
    }
}
