<?php

/**
 * Elimina las columnas 'workspace_statu_id', 'reason_cancel_request_id', 'activity'
 * de la tabla documents
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveWorkspaceStatusIdOnDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {

            // columnas que quiero eliminar
            $removeColums = ['workspace_statu_id', 'reason_cancel_request_id', 'activity'];

            foreach ($removeColums as $key => $column) {
                // Verifico que la tabla tenga una columna '$column' para eliminarla
                if (Schema::hasColumn('documents', $column)) {
                    $table->dropColumn($column);
                }
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
        //
    }
}
