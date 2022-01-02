<?php

/**
 * Modifica el campo 'notify' 
 * Se agrega valor por defecto a false
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 * 
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNotifyFieldToRequiredDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('required_documents', function (Blueprint $table) {

            // Verifico que la tabla no tenga una columna 'notify' para modificarla
            if (!Schema::hasColumn('required_documents', 'notify')) {
                $table->boolean('notify')
                    ->default(0)
                    ->comment('Si se va a notificar cuando este al expirar');
            }


            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('required_documents', function (Blueprint $table) {
            //
        });
    }
}
