<?php

/**
 * Modifica la tabla signer_visits
 * - Se modifica el campo document_id para que permita valores nulos
 *   para los históricos en las solicitudes de archivos

 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFieldDocumentIdOnSignerVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signer_visits', function (Blueprint $table) {
            $table->unsignedBigInteger('document_id')->nullable()->change();
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
