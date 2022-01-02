<?php

/**
 * Información de histórico de acciones sobre un fichero
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            'file_logs',
            function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('file_id')->comment('ID de Fichero');
                $table->string('action')->comment('Acción realizada');
                $table->string('description')->nullable()->comment('Descripción de la acción, si necesario');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('file_logs');
    }
}
