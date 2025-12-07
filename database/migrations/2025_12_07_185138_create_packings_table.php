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
        Schema::create('packings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_byproduk_id')
                ->constrained('kategori_byproduk_cts', 'kategori_byproduk_id')
                ->onDelete('cascade');
            $table->foreignId('kategori_produk_id')
                ->constrained('kategori_produks', 'kategori_produk_id')
                ->onDelete('cascade');
            $table->string('no_batch');
            $table->integer('jumlah_pack')->default(0);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packings');
    }
};
