<?php

/**
 * Adicionar campo para gestión de información asociada a carpeta: ruta completa hasta el fichero.
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPathFieldsToFilesTable extends Migration
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
                $table->text('full_path')->nullable()->comment('Ruta completa hasta el archivo');
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
                $table->dropColumn(['full_path']);
            }
        );
    }
}
