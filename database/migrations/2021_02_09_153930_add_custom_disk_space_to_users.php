<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCustomDiskSpaceToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `users` 
                ADD `custom_disk_space` BIGINT NULL 
                COMMENT 
                    'Espacio de disco personalizado disponible para el usuario en MB. 
                     Por defecto, el usuario tiene los MB disponibles por defecto en su plan. 
                     Aquí se le puede dar cualquier espacio de disco que necesite.' 
                AFTER `config`; "
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
