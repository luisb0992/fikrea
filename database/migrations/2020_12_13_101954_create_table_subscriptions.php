<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `subscriptions` 
            ( 
                `id` BIGINT NOT NULL AUTO_INCREMENT ,
                `user_id` BIGINT NOT NULL COMMENT 'El id del usuario' ,
                `plan_id` BIGINT NOT NULL COMMENT 'El id del plan' ,
                `months` INT NOT NULL DEFAULT '0' COMMENT 'El número de meses adquiridos en la subscripción' ,
                `starts_at` DATE NOT NULL COMMENT 'Fecha de inicio de la subscripción' ,
                `ends_at` DATE NOT NULL COMMENT 'Fecha de finalización de la subscripción' ,
                `canceled_at` DATETIME NULL COMMENT 'Fecha de cancelación de la subscripción' ,
                `payment` DOUBLE NOT NULL DEFAULT '0' COMMENT 'El importe pagado en la subscripción' ,
                `payed_at` DATETIME NULL COMMENT 'Fecha de Pago' ,
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
        Schema::dropIfExists('subscriptions');
    }
}
