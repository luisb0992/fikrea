<?php

/**
 * Elimina las columnas 'workspace_statu_id', 'reason_cancel_request_id', 'activity', 'active'
 * de la tabla document_requests
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveWorkspaceStatusIdOnDocumentRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_requests', function (Blueprint $table) {

            // columnas que quiero eliminar
            $removeColums = ['workspace_statu_id', 'reason_cancel_request_id', 'activity', 'active'];

            foreach ($removeColums as $key => $column) {
                // Verifico que la tabla tenga una columna '$column' para eliminarla
                if (Schema::hasColumn('document_requests', $column)) {
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
