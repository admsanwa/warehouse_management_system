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
        Schema::create('sign', function (Blueprint $table) {
            $table->id();
            $table->string('no_memo', 191);
            $table->string('nik', 50);
            $table->tinyInteger('sign')->comment('0: open, 1: approve, 2: reject');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sign');
    }
};
