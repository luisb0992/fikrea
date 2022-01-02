<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenTitleDescriptionToDocumentSharings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_sharings', function (Blueprint $table) {
            $table->text('token')->nullable()->comment('Token de acceso')->after('type');
            $table->string('title')->nullable()->comment('titulo para la comparticion')->after('type');
            $table->text('description')->nullable()->comment('descripcion de la comparticion')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_sharings', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('title');
            $table->dropColumn('description');
        });
    }
}
