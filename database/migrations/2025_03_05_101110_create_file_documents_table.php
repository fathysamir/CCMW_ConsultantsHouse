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
        Schema::create('file_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_id');
            $table->foreign('file_id')->references('id')->on('project_files')->onDelete('cascade');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('document_id')->nullable();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->unsignedBigInteger('note_id')->nullable();
            $table->foreign('note_id')->references('id')->on('notes')->onDelete('cascade');
            $table->integer('sn')->nullable();
            $table->longText('narrative')->nullable();
            $table->longText('notes1')->nullable();
            $table->longText('notes2')->nullable();
            $table->enum('forClaim', ['0', '1'])->default('1');
            $table->enum('forLetter', ['0', '1'])->default('0');
            $table->enum('forChart', ['0', '1'])->default('0');
            $table->enum('ai_layer', ['0', '1'])->default('0');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_documents');
    }
};
