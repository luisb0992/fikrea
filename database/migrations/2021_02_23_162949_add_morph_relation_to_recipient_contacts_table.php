<?php

/**
 * Modifica la tabla recipient_contacts
 * - Se elimina el campo file_sharing_id
 * + Se adicionan campos history_type y history_id para relación 
 *   con los modelos que lo necesiten en lo adelante :
 *   en este caso FileSharingHistory y RequestDocumentHistory
 *   en funcionalidades de histórico de compartición de archivos
 *   y solicitud de documentos
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMorphRelationToRecipientContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipient_contacts', function (Blueprint $table) {
            $table->dropColumn("file_sharing_id");              // Elimino la columna file_sharing_id
            $table->dropUnique(['file_sharing_id', 'email']);    // Elimino llave única de columnas

            $table->morphs("history");                          // Agrega columna history_type y history_id
                                                                // para relación con varios modelos
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipient_contacts', function (Blueprint $table) {
            //
        });
    }
}
