<?php

/**
 * Adiciona el campo 'type' en la tabla notifications
 * para controlar el visual con que se muestra la notificación
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeFieldToNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'type' para adicionarla
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->tinyInteger('type')
                    ->nullable()
                    ->comment('Tipo de notificación, si de alerta, peligro o información');
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
        Schema::table('notifications', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'type' para eliminarla
            if (Schema::hasColumn('notifications', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
}
