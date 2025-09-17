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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_id');
            $table->bigInteger('store_id');
            $table->bigInteger('item_id');
            $table->string('name');
            $table->string('desc');
            $table->string('unit');
            $table->float('price');
            $table->float('selling_price');
            $table->float('online_price');
            $table->bigInteger('qty');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('orders');
    }
};
