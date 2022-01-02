<?php

/**
 * Se adiciona la columna 'dial_code' en la tabla companies
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDialCodeToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            
            // Verifico que la tabla no tenga una columna 'dial_code' para crearla
            if (!Schema::hasColumn('companies', 'dial_code')) {
                $table->string('dial_code', 5)->nullable();
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
        Schema::table('companies', function (Blueprint $table) {
            
            // Verifico que la tabla tenga una columna 'dial_code' para eliminarla
            if (Schema::hasColumn('companies', 'dial_code')) {
                $table->dropColumn('dial_code');
            }
            
        });
    }
}
