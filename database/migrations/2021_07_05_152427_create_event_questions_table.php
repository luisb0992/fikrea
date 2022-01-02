<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->nullable();
            $table->unsignedBigInteger('event_template_id')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_miltiple')->nullable()->default(false);
            $table->string('response_limit')->nullable();
            $table->boolean('is_left_empty')->nullable()->default(false);
            $table->boolean('answered_with_a_comment')->nullable()->default(false);
            $table->boolean('is_active')->nullable()->default(true);
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
        Schema::dropIfExists('event_questions');
    }
}
