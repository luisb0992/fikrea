<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `companies` 
                (
                 `id` BIGINT NOT NULL AUTO_INCREMENT ,
                 `user_id` BIGINT NOT NULL ,
                 `name` VARCHAR(255) NULL , 
                 `cif` VARCHAR(50) NULL ,
                 `address` TEXT NULL , 
                 `phone` VARCHAR(255) NULL ,
                 `city` VARCHAR(255) NULL , 
                 `province` VARCHAR(255) NULL , 
                 `country` VARCHAR(255) NULL,
                 `created_at` TIMESTAMP NOT NULL,
                 `updated_at` TIMESTAMP NOT NULL, 
                 PRIMARY KEY (`id`)
                )
            "
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
