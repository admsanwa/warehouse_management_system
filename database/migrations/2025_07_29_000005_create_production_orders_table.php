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
        Schema::create('production_order', function (Blueprint $table) {
            $table->id();
            $table->integer('doc_num');
            $table->string('io_no', 50);
            $table->string('prod_no', 50);
            $table->string('prod_desc', 190);
            $table->string('remarks', 255)->nullable();
            $table->date('due_date');
            $table->tinyInteger('status')->comment('0: planned, 1: released');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_order');
    }
};
