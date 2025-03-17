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
        Schema::create('file_documents_tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_document_id');
            $table->foreign('file_document_id')->references('id')->on('file_documents')->onDelete('cascade');
            $table->unsignedBigInteger('contract_tag_id');
            $table->foreign('contract_tag_id')->references('id')->on('contract_tags')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_documents_tags');
    }
};
