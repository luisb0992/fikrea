<?php

/**
 * Adiciona los campos 'sendable_id' y 'sendable_type'
 * en la tabla 'smses'
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMorpheFieldsToSmsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smses', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'sendable_id' y 'sendable_type'
            // para adicionarlas
            if (!Schema::hasColumn('smses', 'sendable_id')
                && !Schema::hasColumn('smses', 'sendable_type')
            ) {
                $table->morphs('sendable');
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
            // Verifico que la tabla tenga una columna 'sendable_id' y 'sendable_type'
            // para eliminarlas
            if (Schema::hasColumn('smses', 'sendable_id')
                && Schema::hasColumn('smses', 'sendable_type')
            ) {
                $table->dropMorphs('sendable');
            }
        });
    }
}
