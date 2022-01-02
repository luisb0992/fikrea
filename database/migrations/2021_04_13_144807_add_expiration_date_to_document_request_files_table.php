<?php

/**
 * Adiciona el campo 'expiration_date' para controlar
 * la fecha de vencimiento del documento que ha introducido el firmante
 * a solicitud del usuario que hace la solicitud
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 * 
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpirationDateToDocumentRequestFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_request_files', function (Blueprint $table) {
            
            // Verifico que la tabla no tenga una columna 'expiration_date' para crearla
            $newColumn = 'expiration_date';
            if (!Schema::hasColumn('document_request_files', $newColumn)) {
                $table->date($newColumn)
                    ->nullable()
                    ->comment('Fecha en que expira el documento');
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
        Schema::table('document_request_files', function (Blueprint $table) {
            
            // Verifico que la tabla tenga una columna 'expiration_date' para eliminarla
            if (Schema::hasColumn('document_request_files', 'expiration_date')) {
                $table->dropColumn('expiration_date');
            }
            
        });
    }
}
