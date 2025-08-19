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
        Schema::create('items_maklon', function (Blueprint $table) {
            $table->id();
            $table->integer('gi');
            $table->integer('gr');
            $table->string('po', 50);
            $table->string('io', 50);
            $table->string('internal_no', 50);
            $table->string('code', 50);
            $table->string('name', 255);
            $table->string('item_group', 50)->nullable();
            $table->string('uom', 50)->nullable();
            $table->integer('in_stock');
            $table->integer('qty');
            $table->integer('stock_min');
            $table->string('scanned_by', 50);
            $table->boolean('is_temp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items_maklon');
    }
};
