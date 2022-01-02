<?php

/**
 * Tabla para guardar la info de los destinatarios que no son contactos del usuario
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipientContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipient_contacts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('file_sharing_id');  // Id de la compartición de archivo
            $table->string('name')->nullable();         // Nombre del destinatario
            $table->string('lastname')->nullable();     // Apellidos del destinatario
            $table->string('email')->nullable();        // Correo del destinatario
            $table->string('phone')->nullable();        // Teléfono del destinatario
            $table->string('dni')->nullable();          // DNI del destinatario
            $table->string('company')->nullable();      // Compañía del destinatario
            $table->string('position')->nullable();     // Cargo del destinatario
            $table->string('token');     // Token del contacto

            $table->unique(['file_sharing_id', 'email']); // El email es único para una compartición

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
        Schema::dropIfExists('recipient_contacts');
    }
}
