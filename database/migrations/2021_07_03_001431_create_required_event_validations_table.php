<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredEventValidationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_event_validations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->boolean('name')->nullable()->default(false);
            $table->boolean('lastname')->nullable()->default(false);
            $table->boolean('dni')->nullable()->default(false);
            $table->boolean('email')->nullable()->default(false);
            $table->boolean('telefono')->nullable()->default(false);
            $table->boolean('address')->nullable()->default(false);
            $table->boolean('postal_code')->nullable()->default(false);
            $table->boolean('photo_facial')->nullable()->default(true);
            $table->boolean('id_facial')->nullable()->default(true);
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
        Schema::dropIfExists('required_event_validations');
    }
}
