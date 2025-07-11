<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('quantity')->default(0);
            $table->date('date');
            $table->integer('price_buy');
            $table->integer('price_sale');
            $table->enum('status', ['request', 'accepted', 'rejected'])->default('request');
            $table->uuid('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->uuid('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ins');
    }
};
