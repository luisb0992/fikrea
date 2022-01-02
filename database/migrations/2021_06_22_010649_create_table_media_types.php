<?php

/**
 * Listado completo de tipo MIME según el registro mantenido por IANA, complementado con el listado utilizado por
 * Apache HTTPD m'as descripción (tomado de http://www.freeformatter.com)
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMediaTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(
            'media_types',
            function (Blueprint $table) {
                $table->id();
                $table->string('media_type')->unique()->comment('Tipo de Fichero');
                $table->string('type')->comment('Tipo');
                $table->string('subtype')->comment('Subtipo');
                $table->string('extensions')->nullable()->comment('Extensiones');
                $table->boolean('signable')->default(false)->comment('¿Se puede firmar?');
                $table->boolean('can_apply_ocr')->default(false)->comment('¿Se le puede aplicar OCR?');
                $table->string('description')->nullable()->comment('Nombre descriptivo');
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
        Schema::dropIfExists('media_types');
    }
}
