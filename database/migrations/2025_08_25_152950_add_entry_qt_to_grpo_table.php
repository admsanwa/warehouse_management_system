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
        Schema::table('grpo', function (Blueprint $table) {
            $table->string('item_desc')->nullable()->after('item_code');
            $table->integer('qty')->nullable()->after('item_desc');
            $table->string('uom')->nullable()->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grpo', function (Blueprint $table) {
            $table->dropColumn(['item_desc', 'qty', 'uom']);
        });
    }
};
