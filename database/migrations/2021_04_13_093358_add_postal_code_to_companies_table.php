<?php

/**
 * Se adiciona la columna 'code_postal' en la tabla companies
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostalCodeToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            
            // Verifico que la tabla no tenga una columna 'code_postal' para crearla
            if (!Schema::hasColumn('companies', 'code_postal')) {
                $table->string('code_postal', 10)->nullable();
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
            
            // Verifico que la tabla tenga una columna 'code_postal' para eliminarla
            if (Schema::hasColumn('companies', 'code_postal')) {
                $table->dropColumn('code_postal');
            }

        });
    }
}
