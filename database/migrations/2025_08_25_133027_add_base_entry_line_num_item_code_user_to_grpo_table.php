<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grpo', function (Blueprint $table) {
            $table->integer('base_entry')->nullable()->after('id');
            $table->integer('line_num')->nullable()->after('base_entry');
            $table->string('item_code')->nullable()->after('line_num');
            $table->unsignedBigInteger('user_id')->nullable()->after('item_code');
        });
    }

    public function down(): void
    {
        Schema::table('grpo', function (Blueprint $table) {
            $table->dropColumn(['base_entry', 'line_num', 'item_code', 'user_id']);
        });
    }
};
