<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verification_forms', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();  // usuario propietario del formulario
            $table->text('comment')->nullable();                // comentario opcional del formulario
            $table->smallInteger('status')->nullable();         // Estado de la verificación
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
        Schema::dropIfExists('verification_forms');
    }
}
