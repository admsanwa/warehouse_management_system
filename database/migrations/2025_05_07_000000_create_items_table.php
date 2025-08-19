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
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // bigint unsigned
            $table->date('posting_date')->nullable();
            $table->string('code', 50)->unique();
            $table->string('name', 255);
            $table->string('group', 190);
            $table->string('uom', 50);
            $table->integer('in_stock');
            $table->integer('stock_min');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
