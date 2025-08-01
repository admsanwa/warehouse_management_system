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
        Schema::create('quality', function (Blueprint $table) {
            $table->id();
            $table->string('io', 190);
            $table->tinyInteger('result')->nullable()->comment('0: NG, 1: OK');
            $table->string('result_by', 50);
            $table->string('remark', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality');
    }
};
