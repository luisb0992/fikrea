<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationFormSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_form_sharings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('verification_form_id');     // la llave de la verificación de datos
            $table->json('signers')->nullable();                    // los usuarios "firmantes" a los que fueron enviados
            $table->dateTime('sent_at')->nullable();                // cuando se compartio

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verification_form_sharings');
    }
}
