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
        Schema::create('lottery_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g., "Ada Kotipathi"
            $table->string('code', 10)->unique(); // e.g., "AK"
            $table->string('name_en', 100)->nullable();
            $table->string('name_si', 100)->nullable();
            $table->string('name_ta', 100)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lottery_types');
    }
};
