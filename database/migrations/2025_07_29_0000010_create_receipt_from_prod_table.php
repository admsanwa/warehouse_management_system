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
        Schema::create('receipt_from_production', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->string('io', 190);
            $table->integer('prod_order');
            $table->string('prod_no', 191);
            $table->string('prod_desc', 255);
            $table->integer('qty');
            $table->string('scanned_by', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_from_production');
    }
};
