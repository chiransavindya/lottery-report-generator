<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lotteries', function (Blueprint $table) {
            $table->string('special1_label')->nullable()->after('special1');
            $table->string('special2_label')->nullable()->after('special2');
            $table->string('special3_label')->nullable()->after('special3');
            $table->string('special4_label')->nullable()->after('special4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lotteries', function (Blueprint $table) {
            $table->dropColumn(['special1_label', 'special2_label', 'special3_label', 'special4_label']);
        });
    }
};
