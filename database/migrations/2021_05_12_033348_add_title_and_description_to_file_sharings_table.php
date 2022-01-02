<?php

/**
 * Nuevos campos para gestión de archivos compartidos: título y descripción
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleAndDescriptionToFileSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table(
            'file_sharings',
            static function (Blueprint $table) {
                $table->string('title')->nullable()->comment('Título de la compartición');
                $table->text('description')->nullable()->comment('Descripción de la compartición');
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
            'file_sharings',
            static function (Blueprint $table) {
                $table->dropColumn(['title', 'description']);
            }
        );
    }
}
