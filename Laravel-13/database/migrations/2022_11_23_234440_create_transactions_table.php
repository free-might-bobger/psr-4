<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('ref_number');
            $table->bigInteger('status_id');
            $table->bigInteger('payment_method_id');
            $table->bigInteger('receive_method_id');
            $table->bigInteger('address_id');
            $table->string('complete_address');
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
            $table->string('contact_number');
            $table->float('delivery_charge');
            $table->timestamp('receive_date')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('transactions');
    }
};
