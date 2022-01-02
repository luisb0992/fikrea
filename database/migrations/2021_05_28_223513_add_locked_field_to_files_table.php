<?php

/**
 * Adicionar información para gestión de fichero: estado de bloqueo.
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockedFieldToFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table(
            'files',
            static function (Blueprint $table) {
                $table->boolean('locked')->default(false)->comment(
                    'Estado de bloqueado del fichero, si el usuario superó el espacio asignado a u suscripción'
                );
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
        Schema::table(
            'files',
            static function (Blueprint $table) {
                $table->dropColumn(['locked']);
            }
        );
    }
}
