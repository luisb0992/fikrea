<?php

/**
 * Crea la tabla process
 * Un proceso es cada una de las validaciones sobre un documento,
 * una solicitud de documentos, una compartición de documentos,
 * que tienen un estado: hecho, pendiente o cancelado,
 * además pueden ser marcados como activo, adicionarles actividad, etc...
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process', function (Blueprint $table) {
            $table->id();

            // Relación con el workspace_statu
            $table->integer('workspace_statu_id')
                ->default(1)                                            // Estado pendiente por defecto
                ->comment('Estado en que se encuentra el proceso');

            // Razón por la que se cancela el proceso
            $table->integer('reason_cancel_request_id')
                ->nullable()
                ->comment('Relación con reason_cancel_requests');

            // Actividad sobre el proceso JSON
            $table->text('activity')
                ->nullable()
                ->comment('JSON - Qué firmantes o quiénes están atendiendo el proceso');

            // Si está activo o no
            $table->boolean('active')
                ->default(0)                                            // Inactivo por defecto
                ->comment('Si se está atendiendo por parte del firmante');

            // Relación con aquellos modelos que tengan un estado y necesiten esta tabla
            $table->morphs('statable');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('process');
    }
}
