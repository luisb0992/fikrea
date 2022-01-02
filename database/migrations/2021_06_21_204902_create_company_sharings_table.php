<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanySharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_sharings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('signature')->nullable()->default(false);
            $table->string('name')->nullable();
            $table->string('cif')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->text('city')->nullable();
            $table->text('province')->nullable();
            $table->string('country')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('dial_code')->nullable();
            $table->string('email')->nullable();

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
        Schema::dropIfExists('company_sharings');
    }
}
