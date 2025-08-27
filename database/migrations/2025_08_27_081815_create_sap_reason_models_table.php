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
        Schema::create('sap_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->nullable()->comment('Category or type of reason');
            $table->string('reason_code', 10)->comment('Reason code');
            $table->string('reason_desc', 191)->comment('Reason description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sap_reasons');
    }
};
