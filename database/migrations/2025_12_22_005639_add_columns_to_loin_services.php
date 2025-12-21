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
        Schema::table('loin_services', function (Blueprint $table) {
            $table->unsignedBigInteger('kategori_1')->nullable();
            $table->decimal('berat_1', 10, 2)->default(0);
            $table->integer('pcs_1')->default(0);

            $table->unsignedBigInteger('kategori_2')->nullable();
            $table->decimal('berat_2', 10, 2)->default(0);
            $table->integer('pcs_2')->default(0);

            $table->unsignedBigInteger('kategori_3')->nullable();
            $table->decimal('berat_3', 10, 2)->default(0);
            $table->integer('pcs_3')->default(0);

            $table->unsignedBigInteger('kategori_4')->nullable();
            $table->decimal('berat_4', 10, 2)->default(0);
            $table->integer('pcs_4')->default(0);

            $table->unsignedBigInteger('kategori_5')->nullable();
            $table->decimal('berat_5', 10, 2)->default(0);
            $table->integer('pcs_5')->default(0);

            $table->unsignedBigInteger('kategori_6')->nullable();
            $table->decimal('berat_6', 10, 2)->default(0);
            $table->integer('pcs_6')->default(0);

            $table->unsignedBigInteger('kategori_7')->nullable();
            $table->decimal('berat_7', 10, 2)->default(0);
            $table->integer('pcs_7')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loin_services', function (Blueprint $table) {
            $table->dropColumn([
                'kategori_1',
                'berat_1',
                'pcs_1',
                'kategori_2',
                'berat_2',
                'pcs_2',
                'kategori_3',
                'berat_3',
                'pcs_3',
                'kategori_4',
                'berat_4',
                'pcs_4',
                'kategori_5',
                'berat_5',
                'pcs_5',
                'kategori_6',
                'berat_6',
                'pcs_6',
                'kategori_7',
                'berat_7',
                'pcs_7',
            ]);
        });
    }
};
