<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableInvoices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `invoices` 
            ( 
              `id` BIGINT NOT NULL AUTO_INCREMENT ,
              `number` VARCHAR(100) NOT NULL COMMENT 'Número de factura' ,
              `subscription_id` BIGINT NOT NULL COMMENT 'La subscripción facturada' ,
              `name` VARCHAR(255) NOT NULL COMMENT 'Nonbre' ,
              `lastname` VARCHAR(255) NULL COMMENT 'Apellidos' ,
              `email` VARCHAR(255) NOT NULL COMMENT 'La dirección de Correo' ,
              `address` VARCHAR(255) NOT NULL COMMENT 'La dirección postal' ,
              `city` VARCHAR(255) NOT NULL COMMENT 'La ciudad' ,
              `province` VARCHAR(255) NOT NULL COMMENT 'La provincia' ,
              `country` VARCHAR(255) NOT NULL COMMENT 'El País' ,
              `phone` VARCHAR(255) NOT NULL COMMENT 'El número de teléfono' ,
              `amount` DOUBLE NOT NULL DEFAULT '0' COMMENT 'El importe de la factura' ,
              `iva` DOUBLE NOT NULL DEFAULT '21' COMMENT 'El IVA' ,
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
        Schema::dropIfExists('invoices');
    }
}
