<?php

/**
 * Tabla común para guardar información de la conección de un usuario
 * como su ip, navegador, sistema operativo, dispositivo
 * y tiempo en que permanece en deteminada pagina
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableConnectionHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'connection_histories',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('document_id'); // Id de documento (Desnormalización)
                $table->string('ip')->nullable();       // Dirección IP
                $table->double('latitude')->nullable(); // Latitud
                $table->double('longitude')->nullable();// Longitud
                $table->text('user_agent');             // Navegador
                $table->string('os');                   // OS
                $table->string('device');               // Dispositivo
                
                // crea campos xxxx_id y xxxx_type para relacion polimórfica con las tablas
                // que necesiten guardar visitas de clientes
                $table->morphs('logable');

                $table->timestamp('starts_at');
                $table->timestamp('ends_at');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('connection_histories');
    }
}
