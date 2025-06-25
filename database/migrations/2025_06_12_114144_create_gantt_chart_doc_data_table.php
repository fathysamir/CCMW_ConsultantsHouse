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
        Schema::create('gantt_chart_doc_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_document_id');
            $table->foreign('file_document_id')->references('id')->on('file_documents')->onDelete('cascade');
            $table->enum('show_cur', ['0', '1'])->default('1');
            $table->enum('cur_type', ['SB', 'DA', 'MS', 'M', 'S'])->default('SB');
            $table->json('cur_sections')->nullable();
            $table->longText('cur_left_caption')->nullable();
            $table->longText('cur_right_caption')->nullable();
            $table->enum('cur_show_sd', ['0', '1'])->default('1');
            $table->enum('cur_show_fd', ['0', '1'])->default('1');
            $table->enum('cur_show_ref', ['l', 'r','non'])->default('r');

            $table->enum('show_pl', ['0', '1'])->default('0');
            $table->enum('pl_type', ['SB', 'M'])->default('SB');
            $table->date('pl_sd')->nullable();
            $table->date('pl_fd')->nullable();
            $table->string('pl_color')->default('00008B');
            $table->longText('pl_left_caption')->nullable();
            $table->longText('pl_right_caption')->nullable();
            $table->enum('pl_show_sd', ['0', '1'])->default('0');
            $table->enum('pl_show_fd', ['0', '1'])->default('0');

            $table->enum('show_lp', ['0', '1'])->default('0');
            $table->date('lp_sd')->nullable();
            $table->date('lp_fd')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gantt_chart_doc_data');
    }
};
