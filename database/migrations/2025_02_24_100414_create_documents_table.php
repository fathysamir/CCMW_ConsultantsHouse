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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->unsignedBigInteger('doc_type_id');
            $table->foreign('doc_type_id')->references('id')->on('doc_types')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->longText('subject');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('from_id')->nullable();
            $table->foreign('from_id')->references('id')->on('stake_holders')->onDelete('cascade');
            $table->unsignedBigInteger('to_id')->nullable();
            $table->foreign('to_id')->references('id')->on('stake_holders')->onDelete('cascade');
            $table->string('reference');
            $table->string('revision')->nullable();
            $table->string('status')->nullable();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('storage_file_id');
            $table->foreign('storage_file_id')->references('id')->on('storage_files')->onDelete('cascade');
            $table->enum('analyzed', ['0', '1'])->default('0');
            $table->enum('analysis_complete', ['0', '1'])->default('0');
             $table->enum('assess_not_pursue', ['0', '1'])->default('0');
            $table->json('threads')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
