<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReasonCancelRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reason_cancel_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reason', 255)->nullable()->comment('motivos para la cancelacion de solicitud de documentos'); // motivos para la cancelacion de solicitud de documentos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reason_cancel_requests');
    }
}
