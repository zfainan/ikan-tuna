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
        Schema::table('packings', function (Blueprint $table) {
            $table->dropColumn(['no_batch', 'jumlah_pack']);

            $table->unsignedBigInteger('penerimaan_id');
            $table->unsignedBigInteger('kategori_byproduk_id')
                ->nullable()
                ->change();
            $table->unsignedBigInteger('kategori_produk_id')
                ->nullable()
                ->change();
            $table->decimal('berat_produk', 8, 2)->default(0.00);
            $table->unsignedInteger('total_produk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packings', function (Blueprint $table) {
            $table->string('no_batch');
            $table->integer('jumlah_pack')->default(0);

            $table->dropColumn(['berat_produk', 'total_produk', 'penerimaan_id']);
        });
    }
};
