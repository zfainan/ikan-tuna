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
        Schema::create('services', function (Blueprint $table) {
            $table->id('service_id');
            $table->date('tgl_service');
            $table->date('tgl_injek_co');
            $table->unsignedBigInteger('penerimaan_id');
            $table->unsignedBigInteger('kategori_byproduk_id');
            $table->string('no_batch');
            $table->json('berat_produk');
            $table->json('total_produk');
            $table->timestamps();

            $table->foreign('penerimaan_id')
                  ->references('penerimaan_id')
                  ->on('penerimaan_ikans')
                  ->onDelete('cascade');

            $table->foreign('kategori_byproduk_id')
                  ->references('kategori_byproduk_id')
                  ->on('kategori_byproduk_cts')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
