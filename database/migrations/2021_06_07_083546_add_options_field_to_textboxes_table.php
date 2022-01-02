<?php

/**
 * Adiciona campo 'options' en la tabla textboxes
 * para guardar las opciones de las cajas tipo select
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionsFieldToTextboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('textboxes', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'options' para adicionarla
            if (!Schema::hasColumn('textboxes', 'options')) {
                $table->text('options')
                    ->nullable()
                    ->comment('Opciones para tipo de caja de texto select');
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
