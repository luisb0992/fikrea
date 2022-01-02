<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkspaceCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspace_comments', function (Blueprint $table) {
            $table->id();
            $table->integer('document_request_id')->nullable()->comment('solicitud de documento'); // Id del documento solicitado
            $table->integer('signer_id')->nullable()->comment('signer al que se le solicita el documento'); // Id del signer
            $table->longText('comment')->nullable()->comment('el comentario dentro del workspace'); // El comentario
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
        Schema::dropIfExists('workspace_comments');
    }
}
