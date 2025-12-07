<div>
    {{-- Style Tabel CSS --}}
    <style>
        .excel-table {
            font-size: 0.75rem;
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }

        .excel-table th,
        .excel-table td {
            border: 1px solid hsl(0, 100.00%, 0.40%);
            padding: 2px 4px;
            vertical-align: middle;
        }

        .excel-input {
            width: 100%;
            height: 22px;
            padding: 0 2px;
            font-size: 0.75rem;
            font-family: 'Arial Narrow', sans-serif;
            border: 1px solid hsl(0, 89.20%, 7.30%);
            border-radius: 3px;
        }

        .excel-input:focus {
            border-color: hsl(0, 89.20%, 7.30%);
            box-shadow: none;
        }

        .excel-table select {
            height: 22px;
            font-size: 0.75rem;
            padding: 0 2px;
            border-radius: 0;
        }

        .excel-table .btn-sm {
            padding: 0 6px;
            height: 22px;
            font-size: 0.7rem;
            line-height: 1;
        }
    </style>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Input Data & Filter --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-auto">
                    <label for="tanggal" class="form-label small">Tanggal Packing</label>
                    <input type="date" id="tanggal" wire:model.live="tanggal"
                        class="form-control form-control-sm @error('tanggal') is-invalid @enderror" required>
                    @error('tanggal')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="kategoriByProdukId" class="form-label small">By Produk</label>
                    <select id="kategoriByProdukId" wire:model.live="kategoriByProdukId"
                        class="form-select form-select-sm @error('kategoriByProdukId') is-invalid @enderror" required>
                        <option value="">Pilih By Produk</option>
                        @if (isset($kategoriByProduk) && $kategoriByProduk->isNotEmpty())
                            @foreach ($kategoriByProduk as $el)
                                <option value="{{ $el->kategori_byproduk_id }}">
                                    {{ $el->nama_produk }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('kategoriByProdukId')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="kategoriProdukId" class="form-label small">Produk</label>
                    <select id="kategoriProdukId" wire:model.live="kategoriProdukId"
                        class="form-select form-select-sm @error('kategoriProdukId') is-invalid @enderror" required>
                        <option value="">Pilih Produk</option>
                        @if (isset($kategoriProduk) && $kategoriProduk->isNotEmpty())
                            @foreach ($kategoriProduk as $el)
                                <option value="{{ $el->kategori_produk_id }}">
                                    {{ $el->nama_produk }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('kategoriProdukId')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <section class="section mt-4">
        <div class="card" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <h4 class="card-title mt-2 text-white">Daftar Cutting by Product</h4>
            </div>

            <div class="card-body mt-2">
                <div class="table-responsive">
                    <table class="table-hover table">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Nomor Batch</th>
                                <th>By Produk</th>
                                <th>Produk</th>
                                <th>Jumlah</th>
                                <th>Tanggal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($packings as $item)
                                <tr style="background: linear-gradient(to right, #f9f9f9 0%, #f0f7ff 100%);">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->no_batch }}</td>
                                    <td>{{ $item->kategoriByProduk?->nama_produk }}</td>
                                    <td>{{ $item->kategoriProduk?->nama_produk }}</td>
                                    <td>{{ $item->jumlah_pack }}</td>
                                    <td>{{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal)->format('d F Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm py-0"
                                            style="font-size:.7rem; height:30px; width:30px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                            @if ($packings->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data packing.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{ $packings->links() }}
            </div>
        </div>
    </section>

    {{-- Button Simpan --}}
    <div class="card-footer px-2 py-1 text-end">
        <button type="button" class="btn btn-secondary btn-sm px-2 py-0" wire:click="print"
            style="font-size: 0.7rem; height: 30px;">
            <i class="bi bi-printer"></i> Print
        </button>

        {{-- ALERT PESAN --}}
        @if (session()->has('message'))
            <div class="alert alert-success mt-3">
                {{ session('message') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('show-error', (message) => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonText: 'OK'
                });
            });

            @this.on('show-success', (message) => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: message,
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Refresh halaman setelah simpan berhasil
                        window.location.reload();
                    }
                });
            });
        });
    </script>
@endpush
