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
        Schema::create('draws', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lottery_type_id')->constrained()->onDelete('cascade');
            $table->date('draw_date');
            $table->integer('draw_number');
            $table->json('numbers'); // Winning numbers
            $table->string('bonus_number')->nullable();
            $table->json('prize_breakdown'); // Prize breakdown with winners
            $table->decimal('total_sales', 15, 2)->nullable();
            $table->decimal('jackpot_amount', 15, 2)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Unique constraint to prevent duplicate draws
            $table->unique(['lottery_type_id', 'draw_date', 'draw_number'], 'unique_draw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draws');
    }
};
