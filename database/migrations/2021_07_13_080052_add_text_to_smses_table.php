<?php

/**
 * Adiciona el campo 'text' en la tabla 'smses'
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTextToSmsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smses', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'text' para adicionarla
            if (!Schema::hasColumn('smses', 'text')) {
                $table->string('text')
                    ->comment("Texto del mensaje")
                    ->after('id');
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
        Schema::table('smses', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'text' para eliminarla
            if (Schema::hasColumn('smses', 'text')) {
                $table->dropColumn('text');
            }
        });
    }
}
