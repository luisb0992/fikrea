<?php

/**
 * Adiciona el campo 'notify' para controlar
 * que se envíen notificaciones al usuario y al firmante
 * de que el documento está próximo a expirar y debe renovar el mismo
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 * 
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotifyToRequiredDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('required_documents', function (Blueprint $table) {
            
            // Verifico que la tabla no tenga una columna 'notify' para crearla
            $newColumn = 'notify';
            if (!Schema::hasColumn('required_documents', $newColumn)) {
                $table->boolean($newColumn)
                    ->nullable()
                    ->comment('Si se va a notificar cuando este al expirar');
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
            
            // Verifico que la tabla tenga una columna 'notify' para eliminarla
            if (Schema::hasColumn('required_documents', 'notify')) {
                $table->dropColumn('notify');
            }

        });
    }
}
