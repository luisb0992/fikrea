<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableRequiredDocumentExamples extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `required_document_examples` 
            ( 
                `id` BIGINT NOT NULL AUTO_INCREMENT ,
                `lang` VARCHAR(2) NULL , 
                `name` MEDIUMTEXT NOT NULL COMMENT 'Nombre del documento, por ejemplo, DNI' , 
                `validity` INT NULL COMMENT 'Una validez para el documento en días o null para validez indeterminada' , 
                `validity_unit` TINYINT NOT NULL DEFAULT '0' 
                    COMMENT 'Unidad de medida del tiempo de validación: 
                        0 = Ninguno, 1= dias, 7 = semanas, 30 = meses, 365 = años', 
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
        Schema::dropIfExists('required_document_examples');
    }
}
