<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTablePlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `plans` 
            ( 
                `id` BIGINT NOT NULL ,
                `name` VARCHAR(255) NOT NULL COMMENT 'Nombre del Plan' ,
                `disk_space` INT NOT NULL COMMENT 'Espacio disponible en MB' ,
                `signers` INT NULL COMMENT 'Número de firmantes por documento, null para infinitos' ,
                `monthly_price` DOUBLE NOT NULL COMMENT 'Precio mensual' ,
                `yearly_price` DOUBLE NOT NULL COMMENT 'Precio anual' ,
                `change_price` DOUBLE NOT NULL DEFAULT '0' 
                    COMMENT 'Precio mensual aplicable por cambio de plan a partir de más de 30 días', 
                `tax` DOUBLE NOT NULL DEFAULT '21.0' COMMENT 'El Iva aplicable' ,
                `trial_period` INT NULL COMMENT 'Periodo de prueba en días' , 
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
        Schema::dropIfExists('plans');
    }
}
