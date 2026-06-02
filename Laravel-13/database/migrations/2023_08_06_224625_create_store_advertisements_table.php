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
        Schema::create('store_advertisements', function (Blueprint $table) {
            $table->id();
            $table->integer('franchisee_id');
            $table->integer('rank');
            $table->integer('store_id');
            $table->float('amount');
            $table->date('start_at');
            $table->date('end_at');
            $table->integer('city_id');
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
        Schema::dropIfExists('store_advertisements');
    }
};
