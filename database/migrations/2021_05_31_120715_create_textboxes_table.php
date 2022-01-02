<?php

/**
 * Crea la tabla 'textboxes'
 * donde guardaremos las cajas de texto que deben completar los firmantes
 * en los procesos de validación de un documento
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTextboxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('textboxes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('document_id')
                ->comment('El documento al que pertenece la caja de texto');

            $table->unsignedBigInteger('signer_id')
                ->comment('El id del firmante');

            $table->string('signer')
                ->comment('El firmante. Puede ser: Apellido y nombre, Dirección de Correo o Teléfono');

            $table->boolean('creator')
                ->default(0)
                ->comment('Si es el creador/autor del documento');

            $table->integer('page')
                ->comment('La paǵina');

            $table->integer('x')
                ->comment('La posición x de la firma dentro de la página');

            $table->integer('y')
                ->comment('La posición y de la firma dentro de la página');

            $table->string('text')
                ->comment('El texto a cumplimentar');

            $table->string('code', 32)
                ->comment('Un id único para cada caja de texto');

            $table->boolean('signed')
                ->default(0)
                ->comment('Si la caja ha sido completada o no');

            $table->datetime('signDate')->nullable()
                ->comment('La fecha en que se ha aportado la info solicitada');

            $table->string('ip', 64)->nullable()
                ->comment('La dirección ip desde la que se ha aportado la info');

            $table->text('user_agent')->nullable()
                ->comment('Agente de usuario utilizado');

            $table->double('latitude')->nullable()
                ->comment('La latitud en el momento de la firma,  datum WGS84');

            $table->double('longitud')->nullable()
                ->comment('La longitud en el momento de la firma,  datum WGS84');

            $table->tinyInteger('device')->nullable()
                ->comment('Dispositivo con que se ha conectado; Móvil, Tablet o PC');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('textboxes');
    }
}
