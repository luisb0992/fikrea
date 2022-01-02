<?php

/**
 * Adiciona el campo activity en la tabla documents
 * para controlar las validaciones que se están atendiendo
 * por los firmantes en un momento determinado
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'activity' para modificarla
            if (!Schema::hasColumn('documents', 'activity')) {
                $table->text('activity')
                    ->nullable()
                    ->comment('JSON - Qué firmantes están atendiendo qué validación');
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
        Schema::table('documents', function (Blueprint $table) {
            //
        });
    }
}
