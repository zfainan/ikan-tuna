<?php

namespace App\Http\Controllers;

use App\Models\Packing;
use Illuminate\Http\Request;

class PackingController extends Controller

{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $packings = Packing::with(['kategoriByProduk', 'kategoriProduk'])->get();

        return view('admin.transaksi.packing', [
            'packings' => $packings,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_byproduk_id' => 'required|exists:kategori_byproduk_cts,kategori_byproduk_id',
            'kategori_produk_id' => 'required|exists:kategori_produks,kategori_produk_id',
            'jumlah_pack' => 'required|null',
            'tanggal' => 'required|date',
            'no_batch' => 'required|string',
        ]);

        // Simpan data cutting
        $packing = new Packing();
        $packing->fill([
            'jumlah_pack' => $validated['jumlah_pack'],
            'tanggal' => $validated['tanggal'],
            'no_batch' => $validated['no_batch'],
        ]);
        $packing->kategoryByProduk()->associate($request->kategori_byproduk_id);
        $packing->kategoryProduk()->associate($request->kategori_produk_id);
        $packing->save();

        return redirect()
            ->route('packings.index')
            ->with('success', 'Packing berhasil ditambahkan.');
    }

    public function update(Request $request, Packing $packing)
    {
        $validated = $request->validate([
            'kategori_byproduk_id' => 'required|exists:kategori_byproduk_cts,kategori_byproduk_id',
            'kategori_produk_id' => 'required|exists:kategori_produks,kategori_produk_id',
            'jumlah_pack' => 'required|null',
            'tanggal' => 'required|date',
            'no_batch' => 'required|string',
        ]);

        // Simpan data cutting
        $packing->fill([
            'jumlah_pack' => $validated['jumlah_pack'],
            'tanggal' => $validated['tanggal'],
            'no_batch' => $validated['no_batch'],
        ]);
        $packing->kategoryByProduk()->associate($request->kategori_byproduk_id);
        $packing->kategoryProduk()->associate($request->kategori_produk_id);
        $packing->save();

        return redirect()
            ->route('packings.index')
            ->with('success', 'Packing berhasil diperbarui.');
    }
}
