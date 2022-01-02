<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableSignerVisits extends Migration
{
    /**
     * Tabla para guardar el histórico de visitas de los firmantes sobre
     * su área de trabajo donde realizará la firma y validaciones
     * correspondientes
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'signer_histories',
            function (Blueprint $table) {
                $table->id();

                $table->unsignedInteger('area')->default(1);
                        // área de trabajo que visita
                        // 1 - workspace home
                        // 2 - workspace page
                        // 3 - workspace signature
                        // 4 - workspace audio
                        // 5 - workspace video
                        // 6 - workspace document
                
                $table->bigInteger('signer_id');        // Id del firmante
                
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('signer_histories');
    }
}
