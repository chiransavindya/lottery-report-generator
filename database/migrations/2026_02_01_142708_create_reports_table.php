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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draw_id')->constrained('draws')->onDelete('cascade');
            $table->foreignId('generated_by')->constrained('users')->onDelete('cascade');
            $table->integer('version')->default(1); // For versioning reports
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->string('pdf_path_en', 512)->nullable(); // English PDF
            $table->string('pdf_path_si', 512)->nullable(); // Sinhala PDF
            $table->string('pdf_path_ta', 512)->nullable(); // Tamil PDF
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
