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
        Schema::create('purchase_order', function (Blueprint $table) {
            $table->id();
            $table->integer('no_po');
            $table->string('internal_no', 50)->nullable();
            $table->string('io', 50);
            $table->string('so', 50);
            $table->string('vendor_code', 50);
            $table->string('vendor', 190);
            $table->string('vendor_ref_no', 50);
            $table->string('contact_person', 50);
            $table->string('buyer', 50);
            $table->string('approved_by', 50);
            $table->string('knowing_by', 50);
            $table->string('contract', 50);
            $table->string('contract_addendum', 50);
            $table->string('note', 250);
            $table->date('posting_date');
            $table->string('status', 50)->comment('1: open, 2: closed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order');
    }
};
