<?php

/**
 * Adiciona varios campos en la tabla textboxes
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFieldsToTextboxsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('textboxes', function (Blueprint $table) {

            // Verifico que la tabla no tenga una columna 'type' para adicionarla
            if (!Schema::hasColumn('textboxes', 'type')) {
                $table->tinyInteger('type')
                    ->default(4)
                    ->comment('Tipo de caja de texto');
            }
            // Verifico que la tabla no tenga una columna 'width' para adicionarla
            if (!Schema::hasColumn('textboxes', 'width')) {
                $table->double('width')
                    ->default(185)
                    ->comment('Ancho de la caja de texto');
            }
            // Verifico que la tabla no tenga una columna 'shiftX' para adicionarla
            if (!Schema::hasColumn('textboxes', 'shiftX')) {
                $table->double('shiftX')
                    ->nullable()
                    ->comment('Posición left relativa del input respecto al div contenedor de la caja de texto');
            }
            // Verifico que la tabla no tenga una columna 'shiftY' para adicionarla
            if (!Schema::hasColumn('textboxes', 'shiftY')) {
                $table->double('shiftY')
                    ->nullable()
                    ->comment('Posición top relativa del input respecto al div contenedor de la caja de texto');
            }
            // Verifico que la tabla no tenga una columna 'fitMaxLength' para adicionarla
            if (!Schema::hasColumn('textboxes', 'fitMaxLength')) {
                $table->boolean('fitMaxLength')
                    ->default(0)
                    ->comment('Si se ajusta la longitud del texto al ancho de la caja');
            }
            // Verifico que la tabla no tenga una columna 'rules' para adicionarla
            if (!Schema::hasColumn('textboxes', 'rules')) {
                $table->text('rules')
                    ->nullable()
                    ->comment('Restricciones de la caja de texto');
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
        Schema::table('textboxes', function (Blueprint $table) {
            //
        });
    }
}
