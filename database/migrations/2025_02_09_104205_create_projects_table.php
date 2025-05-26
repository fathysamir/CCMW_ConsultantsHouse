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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->string('code');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('account_id');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->unsignedBigInteger('old_category_id')->nullable();
            $table->foreign('old_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->date('contract_date')->nullable(true);
            $table->date('commencement_date')->nullable(true);
            $table->longText('condation_contract')->nullable(true);
            $table->double('original_value', 15, 2)->nullable();
            $table->double('revised_value', 15, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('measurement_basis')->nullable();
            $table->longText('notes')->nullable(true);
            $table->longText('summary')->nullable(true);
            $table->enum('status', ['Active', 'Archived', 'Deleted'])->default('Active');

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
