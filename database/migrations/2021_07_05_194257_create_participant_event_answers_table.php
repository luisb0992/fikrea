<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantEventAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participant_event_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('event_id')->nullable();
            $table->unsignedBiginteger('event_paticipants_id')->nullable();
            $table->unsignedBiginteger('event_question_id')->nullable();
            $table->unsignedBiginteger('event_answers_id')->nullable();
            $table->text('answered_with_a_comment')->nullable();
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
        Schema::dropIfExists('participant_event_answers');
    }
}
