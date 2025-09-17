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
        Schema::create('pickup_time', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_id');
            $table->bigInteger('store_id');
            $table->timestamp('date_time')->useCurrent();
            $table->boolean('store_approval')->default(true);
            $table->boolean('customer_approval')->default(false);
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
        Schema::dropIfExists('pickup_time');
    }
};
