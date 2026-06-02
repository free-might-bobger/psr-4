<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_calls', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date');
            $table->string('title');
            $table->text('desc');
            $table->integer('security_question_id');
            $table->text('security_answer');
            $table->integer('scheduled_call_status_id')->default(1);
            $table->integer('created_by');
            $table->integer('updated_by');
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
        Schema::dropIfExists('scheduled_calls');
    }
};
