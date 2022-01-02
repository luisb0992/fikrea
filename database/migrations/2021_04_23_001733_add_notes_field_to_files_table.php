<?php

/**
 * Adicionar campo para gestión de información asociada a carpeta: notas sobre una carpeta
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesFieldToFilesTable extends Migration
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
                $table->string('notes')->nullable()->comment(
                    'Comentarios acerca de la carpeta (para indicar que está asociada a un contacto, por ejemplo)'
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
                $table->dropColumn(['notes']);
            }
        );
    }
}
