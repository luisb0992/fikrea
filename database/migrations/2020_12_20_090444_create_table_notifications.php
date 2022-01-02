<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTableNotifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "CREATE TABLE `notifications` 
            ( `id` BIGINT NOT NULL AUTO_INCREMENT, 
              `user_id` BIGINT NOT NULL ,
              `title` TEXT NOT NULL ,
              `message` TEXT NOT NULL ,
              `url` TEXT NOT NULL, 
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `read_at` DATETIME NULL, 
              PRIMARY KEY (`id`))"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}
