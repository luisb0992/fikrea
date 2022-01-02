<?php

/**
 * Adicionar campos para la gestión de carpetas (caso especial de un fichero)
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFolderFieldsToFilesTable extends Migration
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
                $table->unsignedBigInteger('parent_id')->nullable()->comment(
                    'ID de la carpeta que contiene el archivo; nulo, si está en la raíz.'
                );
                $table->boolean('is_folder')->nullable()->default(false)->comment(
                    'Verdadero si el archivo es una carpeta; falso, en caso contrario.'
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
                $table->dropColumn(['parent_id', 'is_folder']);
            }
        );
    }
}
