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
        Schema::create('paragraphs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('reply_user_id')->nullable();
            $table->foreign('reply_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('slug');
            $table->string('number');
            $table->longText('title_above')->nullable();
            $table->longText('background_ref')->nullable();
            $table->boolean('red_flag')->default(false);
            $table->boolean('green_flag')->default(false);
            $table->boolean('blue_flag')->default(false);
            $table->boolean('replyed')->default(false);
            $table->longText('background')->nullable();
            $table->longText('paragraph');
            $table->longText('reply')->nullable();
            $table->longText('para_exhibits')->nullable();
            $table->longText('reply_exhibits')->nullable();
            $table->longText('para_numbers')->nullable();
            $table->longText('notes')->nullable();
            $table->unsignedBigInteger('para_wise_id');
            $table->foreign('para_wise_id')->references('id')->on('para_wises')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paragraphs');
    }
};
