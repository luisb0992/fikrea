<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToVerificationForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verification_forms', function (Blueprint $table) {
            // Verifico que la tabla no tenga una columna 'name' para adicionarla
            if (!Schema::hasColumn('verification_forms', 'name')) {
                $table->string('name')
                    ->nullable()
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
        Schema::table('verification_forms', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
