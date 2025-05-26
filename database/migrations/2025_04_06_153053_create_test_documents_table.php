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
        Schema::create('test_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doc_type_id')->nullable();
            $table->foreign('doc_type_id')->references('id')->on('doc_types')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->string('subject')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('from_id')->nullable();
            $table->foreign('from_id')->references('id')->on('stake_holders')->onDelete('cascade');
            $table->unsignedBigInteger('to_id')->nullable();
            $table->foreign('to_id')->references('id')->on('stake_holders')->onDelete('cascade');
            $table->string('reference')->nullable();
            $table->string('revision')->nullable();
            $table->string('status')->nullable();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('storage_file_id');
            $table->foreign('storage_file_id')->references('id')->on('storage_files')->onDelete('cascade');
            $table->enum('analyzed', ['0', '1'])->default('0');
            $table->enum('analysis_complete', ['0', '1'])->default('0');
            $table->json('threads')->nullable();
            $table->enum('confirmed', ['0', '1'])->default('0');
            $table->unsignedBigInteger('file_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_documents');
    }
};
