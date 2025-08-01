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
        Schema::create('barcodeqc', function (Blueprint $table) {
            $table->id();
            $table->string('prod_no', 255);
            $table->string('prod_desc', 255);
            $table->integer('qty');
            $table->string('username', 255);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcodeqc');
    }
};
