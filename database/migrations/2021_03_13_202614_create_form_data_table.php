<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_data', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');                  // usuario que envio la validacion
            $table->unsignedBigInteger('signer_id');                // firmante o receptor
            $table->unsignedBigInteger('document_id');              // id del documento
            $table->string('type');                                 // tipo de formulario (particular o empresarial)
            $table->smallInteger('template_number');                // numero de la plantilla
            $table->string('field_name');                           // nombre del campo
            $table->string('field_text')->nullable();               // descripcion o texto del campo
            $table->smallInteger('min')->nullable();                // min de texto aceptado
            $table->smallInteger('max')->nullable();                // maximo de texto aceptado
            $table->string('character_type')->nullable();           // tipo de caracter (numerico, texto, expecial....)
            $table->string('ip')->nullable();                       // ip de donde se valida
            $table->text('user_agent')->nullable();                 // User Agent o informacion del SO
            $table->double('latitude')->nullable();                 // Latitud
            $table->double('longitude')->nullable();                // Longitud
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
        Schema::dropIfExists('form_data');
    }
}
