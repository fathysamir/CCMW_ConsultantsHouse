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
        Schema::create('document_analysis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->enum('time_prolongation_cost', ['0', '1'])->default('0');
            $table->enum('disruption_cost', ['0', '1'])->default('0');
            $table->enum('variation', ['0', '1'])->default('0');
            $table->longText('impacted_zone')->nullable();
            $table->longText('concerned_part')->nullable();
            $table->longText('why_need_analysis')->nullable();
            $table->longText('affected_works')->nullable();
            $table->date('analysis_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_analysis');
    }
};
