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
        Schema::table('doc_types', function (Blueprint $table) {
            $table->unsignedBigInteger('from')->nullable();
            $table->foreign('from')->references('id')->on('stake_holders')->onDelete('cascade');
            $table->unsignedBigInteger('to')->nullable();
            $table->foreign('to')->references('id')->on('stake_holders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doc_types', function (Blueprint $table) {
            $table->dropColumn('from');
            $table->dropColumn('to');
        });
    }
};
