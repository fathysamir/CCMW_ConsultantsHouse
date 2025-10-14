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
        Schema::create('windows', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60);
            $table->integer('no');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('duration')->default(0);
            $table->integer('culpable')->nullable();
            $table->integer('excusable')->nullable();
            $table->integer('compensable')->nullable();
            $table->integer('transfer_compensable')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('windows');
    }
};
