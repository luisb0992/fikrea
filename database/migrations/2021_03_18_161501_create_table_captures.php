<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableCaptures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `captures` (
                `id` BIGINT NOT NULL AUTO_INCREMENT,
                `user_id` bigint NOT NULL,
                `signer_id` bigint NOT NULL COMMENT 'El id del firmante',
                `document_id` bigint NOT NULL COMMENT 'El id del documento',
                `path` text NOT NULL COMMENT 'La ruta del archivo en la carpeta de videos',
                `type` text NOT NULL COMMENT 'El tipo mime del archivo',
                `size` bigint NOT NULL COMMENT 'El tamaño del archivo en bytes',
                `duration` varchar(5) NOT NULL COMMENT 'La duración del archivo en formato mm:ss',
                `ip` varchar(64) DEFAULT NULL COMMENT 'La dirección IP',
                `user_agent` text COMMENT 'El agente de usuario',
                `latitude` double DEFAULT NULL COMMENT 'La latitud',
                `longitude` double DEFAULT NULL COMMENT 'La longitud',
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
              )"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('captures');
    }
}
