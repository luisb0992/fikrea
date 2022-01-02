<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentSharingContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_sharing_contacts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('document_sharing_id');  // Id de la compartición del documento
            $table->string('name')->nullable();                 // Nombre del destinatario
            $table->string('lastname')->nullable();             // Apellidos del destinatario
            $table->string('email')->nullable();                // Correo del destinatario
            $table->string('phone')->nullable();                // Teléfono del destinatario
            $table->string('dni')->nullable();                  // DNI del destinatario
            $table->string('company')->nullable();              // Compañía del destinatario
            $table->string('position')->nullable();             // Cargo del destinatario
            $table->string('token')->nullable();                // Token del contacto

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
        Schema::dropIfExists('document_sharing_contacts');
    }
}
