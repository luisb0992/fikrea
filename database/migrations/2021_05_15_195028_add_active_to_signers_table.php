<?php

/**
 * Adiciona el campo 'active' en la tabla signers
 * donde guardaremos si el firmante está en su workspace
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActiveToSignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signers', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'active' para adicionarla
            if (!Schema::hasColumn('signers', 'active')) {
                $table->boolean('active')
                    ->default(0)
                    ->comment('Si el firmante está en su workspace.');
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
        Schema::table('signers', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'active' para eliminarla
            if (Schema::hasColumn('signers', 'active')) {
                $table->dropColumn('active');
            }
        });
    }
}
