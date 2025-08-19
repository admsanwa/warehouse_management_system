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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('no_po')->nullable();
            $table->integer('grpo')->nullable();
            $table->integer('prod_order')->nullable();
            $table->integer('isp')->nullable();
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade');
            $table->integer('qty')->nullable();
            $table->integer('stock')->nullable();
            $table->integer('stock_in')->nullable();
            $table->integer('stock_out')->nullable();
            $table->integer('on_hand')->nullable();
            $table->foreignId('scanned_by')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('note', 99)->nullable();
            $table->boolean('is_temp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
