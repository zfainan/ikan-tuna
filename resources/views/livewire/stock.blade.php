<div>
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
        <div class="card-header px-3 py-2 text-white"
            style="background: linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); font-size: 0.85rem;">
            <i class="bi bi-pencil-square me-1"></i>Form Filter Data Stock
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-auto">
                    <label for="session_tgl_packing" class="form-label small">Tanggal Packing</label>
                    <input type="date" id="session_tgl_packing" wire:model.live="session_tgl_packing"
                        class="form-control form-control-sm @error('session_tgl_packing') is-invalid @enderror"
                        required>
                    @error('session_tgl_packing')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Status Sesi --}}
    <div class="row mt-3">
        <div class="col-12">
            @if ($session_tgl_packing)
                <div class="rounded-3 p-2 text-white shadow-sm"
                    style="background: linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); font-size: 0.75rem;">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    <strong>Sesi Aktif:</strong>
                    <div class="mt-1">
                        <div class="row">
                            <div class="col-4 text-start">
                                <div>
                                    <strong>Tanggal Packing:</strong>
                                    {{ \Carbon\Carbon::parse($session_tgl_packing)->format('d F Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-3 p-2 text-white shadow-sm"
                    style="background:linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); border: 1px solid rgb(255, 255, 255); font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    @if (!$session_tgl_packing)
                        Pilih tanggal packing terlebih dahulu.
                    @else
                        Lengkapi semua data sesi terlebih dahulu.
                    @endif
                </div>
            @endif
        </div>
    </div>

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
            border: none;
            border-radius: 3px;
        }

        .excel-input:focus {
            outline: none;
            border: none;
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

    {{-- ======== TABEL INPUT DETAIL (berat & pcs) + Tombol Tambah & Simpan ======== --}}
    <div class="d-flex justify-content-center my-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-1" style="max-width: 450px;">
            <span class="fw-semibold"
                style="font-size: 1.3rem; font-family: 'Copperplate', fantasy; color:rgb(16, 10, 10); letter-spacing: 1px; text-transform: uppercase;">
                <img src="/img/Logo.png" alt="Logo" width="100" height="100">
                Tally Stock
            </span>
        </div>
    </div>

    <div class="card-body p-1">
        <div class="card mb-2 shadow-sm">
            <div class="table-responsive">
                <table class="excel-table">
                    <thead class="table-light text-center align-middle" style="background-color:rgb(121, 173, 246);">
                        <tr>
                            <th rowspan="2" style="width:80px;">No.</th>
                            <th rowspan="2">Produk</th>
                            {{-- Produk --}}
                            <th colspan="2">
                                Packing Masuk
                            </th>
                            <th colspan="2">
                                Stok Akhir
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 120px;">Berat (Kg)</th>
                            <th style="width: 120px;">Total (Pcs)</th>
                            <th style="width: 120px;">Berat (Kg)</th>
                            <th style="width: 120px;">Total (Pcs)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr>
                                {{-- No. Batch --}}
                                <td class="text-center">
                                    {{ $index + 1 }}.
                                </td>
                                <td>
                                    {{ $row['product'] }}
                                </td>
                                {{-- Berat & Total Produk --}}
                                <td class="text-center">
                                    {{ $row['incoming_berat'] }}
                                </td>
                                <td class="text-center">
                                    {{ $row['incoming_pcs'] }}
                                </td>
                                <td class="text-center">
                                    {{ $row['total_berat'] }}
                                </td>
                                <td class="text-center">
                                    {{ $row['total_pcs'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        {{-- Total --}}
                        <tr class="table-secondary fw-bold excel-input text-center"
                            style="background-color:rgb(121, 173, 246);">
                            <td colspan="2">Total</td>
                            <td>{{ number_format($incoming_total_berat ?? 0, 2) }} kg</td>
                            <td>{{ number_format($incoming_total_pcs ?? 0, 0) }} pcs</td>
                            <td>{{ number_format($total_berat ?? 0, 2) }} kg</td>
                            <td>{{ number_format($total_pcs ?? 0, 0) }} pcs</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Button Simpan --}}
    <div class="card-footer px-2 py-1 text-end">
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
