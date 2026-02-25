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
        Schema::create('upload_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('upload_batches')->onDelete('cascade');
            $table->string('filename', 255); // Secure filename
            $table->string('original_filename', 255); // Original user filename
            $table->string('file_path', 512); // Full path
            $table->bigInteger('file_size'); // File size in bytes
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('checksum', 64)->unique(); // MD5 or SHA256 hash
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_files');
    }
};
