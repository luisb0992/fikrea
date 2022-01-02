<?php

/**
 * Adiciona el campo 'has_expiration_date' para controlar
 * si el documento tiene fecha de expiracion
 * la cual debe introducir el firmante a solicitud del usuario.
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 * 
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpirationDateToRequiredDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('required_documents', function (Blueprint $table) {
            
            // Verifico que la tabla no tenga una columna 'has_expiration_date' para crearla
            $newColumn = 'has_expiration_date';
            if (!Schema::hasColumn('required_documents', $newColumn)) {
                $table->boolean($newColumn)
                    ->nullable()
                    ->comment('Si el firmante debe especificar una fecha de caducidad');;
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
        Schema::table('required_documents', function (Blueprint $table) {

            // Verifico que la tabla tenga una columna 'has_expiration_date' para eliminarla
            if (Schema::hasColumn('required_documents', 'has_expiration_date')) {
                $table->dropColumn('has_expiration_date');
            }

        });
    }
}
