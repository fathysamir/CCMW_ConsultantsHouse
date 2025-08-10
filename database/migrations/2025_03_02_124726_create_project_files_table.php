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
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->BigInteger('code');
            $table->string('name');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->unsignedBigInteger('against_id')->nullable();
            $table->foreign('against_id')->references('id')->on('stake_holders')->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('folder_id');
            $table->foreign('folder_id')->references('id')->on('project_folders')->onDelete('cascade');
            $table->unsignedBigInteger('older_folder_id')->nullable();
            $table->foreign('older_folder_id')->references('id')->on('project_folders')->onDelete('cascade');
            $table->longText('notes')->nullable();
            $table->integer('analyses_complete')->nullable();

            $table->enum('time', ['0', '1'])->default('0');
            $table->enum('prolongation_cost', ['0', '1'])->default('0');
            $table->enum('disruption_cost', ['0', '1'])->default('0');
            $table->enum('variation', ['0', '1'])->default('0');
            $table->enum('closed', ['0', '1'])->default('0');
            $table->enum('assess_not_pursue', ['0', '1'])->default('0');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_files');
    }
};
