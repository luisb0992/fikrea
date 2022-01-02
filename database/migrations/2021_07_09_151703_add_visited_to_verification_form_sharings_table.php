<?php

/**
 * Adiciona el campo 'visited_at' en la tabla 'verification_form_sharings'
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisitedToVerificationFormSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verification_form_sharings', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'visited_at' para adicionarla
            if (!Schema::hasColumn('verification_form_sharings', 'visited_at')) {
                $table->datetime('visited_at')
                    ->nullable()
                    ->comment("Fecha y hora en que se visitó el workspace desde el enlace enviado")
                    ->after('sent_at');
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
        Schema::table('verification_form_sharings', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'visited_at' para eliminarla
            if (Schema::hasColumn('verification_form_sharings', 'visited_at')) {
                $table->dropColumn('visited_at');
            }
        });
    }
}
