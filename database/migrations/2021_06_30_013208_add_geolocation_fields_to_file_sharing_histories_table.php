<?php

/**
 * Adiciona los campos para almacenar información de geolocalización y dispositivo desde el que se establece la
 * conexión en la tabla de histórico de compartido.
 *
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGeolocationFieldsToFileSharingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table(
            'file_sharing_histories',
            static function (Blueprint $table) {
                $table->double('latitude')->nullable()->comment('Latitud');
                $table->double('longitude')->nullable()->comment('Longitud');
                $table->tinyInteger('device')->nullable()->comment(
                    'Dispositivo con que se ha conectado: Móvil, Tablet o PC'
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table(
            'file_sharing_histories',
            static function (Blueprint $table) {
                $table->dropColumn(['latitude', 'longitude', 'device']);
            }
        );
    }
}
