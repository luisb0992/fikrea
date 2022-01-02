<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class TableSignerHistoriesToSignerVisits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Elimina la tabla signer_histories a signer_visits
        DB::statement("DROP TABLE `signer_histories`");

        // ELiminar tabla connection_histories
        DB::statement("DROP TABLE connection_histories;");

        // Modifica los campos

        DB::statement(
            "CREATE TABLE `signer_visits` (
                `id` bigint UNSIGNED NOT NULL,
                `document_id` bigint NOT NULL,
                `signer_id` bigint NOT NULL,
                `ip` varchar(64) NULL,
                `user_agent` text,
                `latitude` double DEFAULT NULL,
                `longitude` double DEFAULT NULL,
                `starts_at` datetime DEFAULT NULL,
                `ends_at` datetime DEFAULT NULL,
                `request` text NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB"
        );
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement(
            "CREATE TABLE `signer_histories` (
                `id` bigint UNSIGNED NOT NULL,
                `area` int UNSIGNED NOT NULL DEFAULT '1',
                `signer_id` bigint NOT NULL,
                `created_at` timestamp NULL DEFAULT NULL,
                `updated_at` timestamp NULL DEFAULT NULL
              ) ENGINE=InnoDB DEFAULT;"
        );

        DB::statement(
            "CREATE TABLE `connection_histories` (
                `id` bigint UNSIGNED NOT NULL,
                `document_id` bigint UNSIGNED NOT NULL,
                `ip` varchar(255) DEFAULT NULL,
                `latitude` double DEFAULT NULL,
                `longitude` double DEFAULT NULL,
                `user_agent` text NOT NULL,
                `os` varchar(255) NOT NULL,
                `device` varchar(255) NOT NULL,
                `logable_type` varchar(255) NOT NULL,
                `logable_id` bigint UNSIGNED NOT NULL,
                `starts_at` timestamp NOT NULL,
                `ends_at` timestamp NOT NULL
          ) ENGINE=InnoDB DEFAULT "
        );
    }
}
