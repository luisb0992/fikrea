<?php

/**
 * Adiciona el campo activity en la tabla document_requests
 * para controlar el o los firmantes que están aportando
 * documentos en un momento determinado
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivityToDocumentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'activity' para modificarla
            if (!Schema::hasColumn('document_requests', 'activity')) {
                $table->text('activity')
                    ->nullable()
                    ->comment('JSON - Qué firmantes están aportando documentos');
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
