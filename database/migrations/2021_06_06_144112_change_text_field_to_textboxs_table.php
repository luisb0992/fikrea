<?php

/**
 * Modifica el campo campo 'text' en la tabla textboxes
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTextFieldToTextboxsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('textboxes', function (Blueprint $table) {
            // Verifico que la tabla tenga una columna 'text' para adicionarla
            if (Schema::hasColumn('textboxes', 'text')) {
                $table->string('text')
                    ->nullable()->change();
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
