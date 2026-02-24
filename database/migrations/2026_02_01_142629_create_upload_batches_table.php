<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upload_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lottery_type_id')->nullable()->constrained('lottery_types')->onDelete('cascade');
            $table->string('batch_name', 255);
            $table->date('draw_date')->nullable();
            $table->integer('total_files')->default(0);
            $table->integer('processed_files')->default(0);
            $table->integer('failed_files')->default(0);
            $table->enum('status', ['pending', 'processing', 'completed', 'completed_with_errors', 'failed'])->default('pending');
            $table->boolean('is_complete')->default(false);
            $table->json('missing_lotteries')->nullable();
            $table->json('date_buckets')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_batches');
    }
};
