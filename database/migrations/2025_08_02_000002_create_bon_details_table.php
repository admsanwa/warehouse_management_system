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
        Schema::create('bon_details', function (Blueprint $table) {
            $table->id();
            $table->integer('bon_id');
            $table->string('item_code', 50);
            $table->string('item_name', 190);
            $table->integer('qty');
            $table->string('uom', 50);
            $table->string('remark', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon_details');
    }
};
