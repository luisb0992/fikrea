<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWorkspaceStatuIdToSignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('signers', function (Blueprint $table) {
            $table->integer('workspace_statu_id')->default(1)->comment('relacion del signer con el status para el workspace'); // Id de workspace_status
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('signers', function (Blueprint $table) {
            //
        });
    }
}
