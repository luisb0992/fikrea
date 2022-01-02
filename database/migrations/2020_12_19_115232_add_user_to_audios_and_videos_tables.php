<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddUserToAudiosAndVideosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            "ALTER TABLE `audios` ADD `user_id` BIGINT NOT NULL AFTER `id`"
        );

        DB::statement(
            "ALTER TABLE `videos` ADD `user_id` BIGINT NOT NULL AFTER `id`"
        );

        DB::statement(
            "ALTER TABLE `passports` ADD `user_id` BIGINT NOT NULL AFTER `id`"
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
