<?php

/**
 * Adiciona el campo active en la tabla validations
 * para controlar si la validación se está respondiendo
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveToValidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('validations', function (Blueprint $table) {
            
            // Verifico que la tabla no tenga una columna 'active' para modificarla
            if (!Schema::hasColumn('validations', 'active')) {
                $table->boolean('active')
                    ->default(0)
                    ->comment('Si se esta atendiendo la validacion por parte del firmante');
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
        Schema::table('validations', function (Blueprint $table) {
            //
        });
    }
}
