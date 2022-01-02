<?php

/**
 * Adiciona el campo active en la tabla document_requests
 * para controlar si la solicitud de documentos se está atendiendo por
 * el usuario al que se le solicita la misma
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveToDocumentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'active' para modificarla
            if (!Schema::hasColumn('document_requests', 'active')) {
                $table->boolean('active')
                    ->default(0)
                    ->comment('Si se esta atendiendo la solicitud por parte del firmante');
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
        Schema::table('document_requests', function (Blueprint $table) {
            //
        });
    }
}
