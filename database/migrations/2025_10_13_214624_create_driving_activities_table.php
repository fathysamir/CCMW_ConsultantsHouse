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
        Schema::create('driving_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('activity_id');
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->unsignedBigInteger('window_id');
            $table->foreign('window_id')->references('id')->on('windows')->onDelete('cascade');
            $table->enum('program', ['BAS', 'IMP', 'UPD', 'BUT'])->default('BAS');
            $table->date('ms_come_date')->nullable();
            $table->enum('liability', ['Excusable', 'Culpable', 'Neutral', 'Excusable took longer than estimated'])->nullable();
            $table->unsignedBigInteger('milestone_id')->nullable();
            $table->foreign('milestone_id')->references('id')->on('milestones')->onDelete('cascade');
            $table->unsignedBigInteger('file_id')->nullable();
            $table->foreign('file_id')->references('id')->on('project_files')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driving_activities');
    }
};
