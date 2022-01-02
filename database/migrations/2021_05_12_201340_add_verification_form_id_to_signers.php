<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationFormIdToSigners extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signers', function (Blueprint $table) {
            $table->unsignedBigInteger('verification_form_id')
                ->nullable()
                ->after('document_request_id')
                ->comment('campo que identifica si el signer pertenece a una verificación de datos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signers', function (Blueprint $table) {
            $table->dropColumn('verification_form_id');
        });
    }
}
