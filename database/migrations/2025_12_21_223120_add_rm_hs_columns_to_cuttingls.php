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
        Schema::table('cuttingls', function (Blueprint $table) {
            // RM Service
            $table->unsignedBigInteger('rm_grade_1')->nullable();
            $table->unsignedBigInteger('rm_grade_2')->nullable();
            $table->unsignedBigInteger('rm_grade_3')->nullable();
            $table->decimal('rm_berat_1', 10, 2)->default(0);
            $table->decimal('rm_berat_2', 10, 2)->default(0);
            $table->decimal('rm_berat_3', 10, 2)->default(0);

            // Hasil Service
            $table->unsignedBigInteger('hs_grade_1')->nullable();
            $table->unsignedBigInteger('hs_grade_2')->nullable();
            $table->unsignedBigInteger('hs_grade_3')->nullable();
            $table->decimal('hs_berat_1', 10, 2)->default(0);
            $table->decimal('hs_berat_2', 10, 2)->default(0);
            $table->decimal('hs_berat_3', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cuttingls', function (Blueprint $table) {
            // RM Service
            $table->dropColumn([
                'rm_grade_1',
                'rm_grade_2',
                'rm_grade_3',
                'rm_berat_1',
                'rm_berat_2',
                'rm_berat_3',
            ]);

            // Hasil Service
            $table->dropColumn([
                'hs_grade_1',
                'hs_grade_2',
                'hs_grade_3',
                'hs_berat_1',
                'hs_berat_2',
                'hs_berat_3',
            ]);
        });
    }
};
