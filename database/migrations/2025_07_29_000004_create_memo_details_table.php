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
        Schema::create('memo_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('memo_id')->constrained('memo')->onDelete('cascade');
            $table->string('needs', 255)->nullable();
            $table->string('unit', 255)->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('qty')->nullable();
            $table->string('uom', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memo_details');
    }
};
