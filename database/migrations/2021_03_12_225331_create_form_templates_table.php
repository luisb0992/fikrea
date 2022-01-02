<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_templates', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();  // Id del usuario dueño de la plantilla
                                                                // si esta lleno es una plantilla de usuario 
                                                                // sino es una plantilla del sistema

            $table->smallInteger('type');                       // tipo de formulario (particular o empresarial)
            $table->smallInteger('template_number');            // numero de la plantilla
            $table->string('field_name');                       // nombre del campo
            $table->string('field_text')->nullable();           // descripcion o texto del campo
            $table->smallInteger('min')->nullable();            // min de texto aceptado
            $table->smallInteger('max')->nullable();            // maximo de texto aceptado
            $table->string('character_type')->nullable();       // tipo de caracter (numerico, texto, expecial....)
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
        Schema::dropIfExists('form_templates');
    }
}
