<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `orders` 
                ( 
                `id` BIGINT NOT NULL AUTO_INCREMENT ,
                `approved` TINYINT(1) NOT NULL DEFAULT '0' 
                    COMMENT '0 = Si no ha aprobado el pago, 1= Si se ha aprobado el pago', 
                `payed` TINYINT(1) NOT NULL DEFAULT '0' 
                    COMMENT '0 = Si no ha efectuado el pago, 1= Si se ha efectuado el pago',
                `type` TINYINT(1) NOT NULL DEFAULT '0' 
                    COMMENT 'El caso aplicable, 0 = mantenimiento, +1 = ampliación, -1 = devaluación' ,
                `order` VARCHAR(100) NOT NULL COMMENT 'El número de la orden pedido' ,
                `token` VARCHAR(255) NOT NULL COMMENT 'El token' ,
                `user_id` BIGINT NOT NULL COMMENT 'El id del usuario' ,
                `subscription_id` BIGINT NOT NULL COMMENT 'El id de la subscripción' ,
                `plan_id` INT NOT NULL COMMENT 'El id del plan' ,
                `months` INT NOT NULL COMMENT 'El número de meses contratados' ,
                `price` DOUBLE NOT NULL COMMENT 'El precio unitario' ,
                `units` DOUBLE NOT NULL COMMENT 'El número de unidades adquiridas' ,
                `amountTaxExcluded` DOUBLE NOT NULL COMMENT 'El importe sin impuestos' ,
                `tax` DOUBLE NOT NULL COMMENT 'El impuesto aplicable' ,
                `amount` DOUBLE NOT NULL COMMENT 'El importe con impuestos' ,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'La fecha de creación' ,
                `payed_at` DATETIME NULL COMMENT 'Fecha en la que se ha efectuado el pago' , 
                PRIMARY KEY (`id`))
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
        Schema::dropIfExists('orders');
    }
}
