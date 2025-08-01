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
        Schema::create('bon', function (Blueprint $table) {
            $table->id();
            $table->string('no', 50);
            $table->date('date');
            $table->string('section', 50);
            $table->string('io', 50)->nullable();
            $table->string('project', 190)->nullable();
            $table->string('make_to', 50)->nullable();
            $table->string('created_by', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bon');
    }
};
