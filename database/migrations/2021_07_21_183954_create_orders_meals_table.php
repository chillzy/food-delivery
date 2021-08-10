<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersMealsTable extends Migration
{
    public function up(): void
    {
        Schema::create('orders_meals', function (Blueprint $table) {
            $table->id();
            $table->uuid('order_id');
            $table->unsignedBigInteger('meal_id');
            $table->unsignedBigInteger('meal_quantity');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('meal_id')->references('id')->on('meals');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders_meals');
    }
}
