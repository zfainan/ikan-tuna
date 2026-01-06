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
            <i class="bi bi-pencil-square me-1"></i>Form Input Data Service
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-auto">
                    <label for="session_tgl_service" class="form-label small">Tanggal Service</label>
                    <input type="date" id="session_tgl_service" wire:model.live="session_tgl_service"
                        class="form-control form-control-sm @error('session_tgl_service') is-invalid @enderror"
                        required>
                    @error('session_tgl_service')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="selectedTanggalPenerimaan" class="form-label small">Tanggal Penerimaan</label>
                    <select id="selectedTanggalPenerimaan" wire:model.live="selectedTanggalPenerimaan"
                        wire:change="updateSelectedTanggalPenerimaan($event.target.value)"
                        class="form-select form-select-sm @error('selectedTanggalPenerimaan') is-invalid @enderror"
                        @if (!$session_tgl_service) disabled @endif required>
                        <option value="">Pilih Tanggal Penerimaan</option>
                        @if (isset($penerimaan_ikan) && $penerimaan_ikan->isNotEmpty())
                            @foreach ($penerimaan_ikan as $penerimaan)
                                <option value="{{ $penerimaan->penerimaan_id }}">
                                    {{ \Carbon\Carbon::parse($penerimaan->tgl_penerimaan)->format('d F Y') }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('selectedTanggalPenerimaan')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="selectedCuttingByl" class="form-label small">Tanggal Cutting</label>
                    <select id="selectedCuttingByl" wire:model.live="selectedCuttingByl"
                        wire:change="updateSelectedCuttingByl($event.target.value)"
                        class="form-select form-select-sm @error('selectedCuttingByl') is-invalid @enderror"
                        @if (!$selectedTanggalPenerimaan) disabled @endif required>
                        <option value="">Pilih Tanggal Cutting</option>
                        @if (isset($cutting_ikan) && $cutting_ikan->isNotEmpty())
                            @foreach ($cutting_ikan->unique('tgl_cutting') as $cutting)
                                <option value="{{ $cutting->cuttingl_id }}">
                                    {{ \Carbon\Carbon::parse($cutting->tgl_cutting)->format('d F Y') }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('selectedCuttingByl')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="penerimaan_id" class="form-label small">Supplier</label>
                    <select id="penerimaan_id" wire:model.live="penerimaan_id"
                        class="form-select form-select-sm @error('penerimaan_id') is-invalid @enderror"
                        @if (empty($selectedCuttingByl)) disabled @endif required>
                        <option value="">Pilih Supplier</option>
                        @if (isset($cutting_ikan) && $cutting_ikan->isNotEmpty())
                            @foreach ($cutting_ikan->unique('penerimaan_id') as $cutting)
                                @if ($cutting->penerimaan)
                                    <option value="{{ $cutting->penerimaan_id }}">
                                        @php
                                            $jenis = $cutting->penerimaan->jenis_penerimaan;
                                            $supplier =
                                                $cutting->penerimaan->supplier?->nama_supplier ?? 'Tidak ada supplier';
                                            $alamat = $cutting->penerimaan->supplier?->alamat ?? 'Tidak ada alamat';
                                            $displayText = $jenis . '  ' . $alamat . '  ' . $supplier;
                                        @endphp
                                        {{ $displayText }}
                                    </option>
                                @endif
                            @endforeach
                        @endif
                    </select>
                    @error('penerimaan_id')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Status Sesi --}}
    <div class="row mt-3">
        <div class="col-12">
            @if ($session_tgl_service && $selectedTanggalPenerimaan && $selectedCuttingByl && $penerimaan_id)
                @php
                    $selectedPenerimaan = $penerimaan_ikan->firstWhere('penerimaan_id', $penerimaan_id);
                @endphp
                <div class="rounded-3 p-2 text-white shadow-sm"
                    style="background: linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); font-size: 0.75rem;">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    <strong>Sesi Aktif:</strong>
                    <div class="mt-1">
                        <div class="row">
                            <div class="col-4 text-start">
                                <div><strong>Tanggal Service:</strong>
                                    {{ \Carbon\Carbon::parse($session_tgl_service)->format('d F Y') }}</div>
                                <div><strong>Tanggal Penerimaan:</strong>
                                    {{ \Carbon\Carbon::parse($selectedPenerimaan?->tgl_penerimaan)->format('d F Y') }}
                                </div>
                            </div>
                            <div class="col-4 text-start">
                                @if ($selectedPenerimaan)
                                    <div><strong>Tanggal Cutting:</strong>
                                        {{ \Carbon\Carbon::parse($selectedPenerimaan->tgl_cutting)->format('d F Y') }}
                                    </div>
                                    <div><strong>Supplier:</strong>
                                        {{ $selectedPenerimaan->supplier?->nama_supplier ?? '-' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="rounded-3 p-2 text-white shadow-sm"
                    style="background:linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); border: 1px solid rgb(255, 255, 255); font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i>
                    @if (!$session_tgl_service)
                        Pilih tanggal service terlebih dahulu.
                    @elseif(!$selectedTanggalPenerimaan)
                        Pilih tanggal penerimaan untuk melanjutkan input data.
                    @elseif(!$selectedCuttingByl)
                        Pilih tanggal cutting untuk melanjutkan input data.
                    @elseif(!$penerimaan_id)
                        Pilih jenis penerimaan untuk melanjutkan input data.
                    @else
                        Tidak ada data sesi aktif.
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
                <img src="/img/Logo.png" alt="Logo" width="100" height="100"> Tally Service Loin</span>
        </div>
    </div>

    <div class="card-body p-1">
        <button class="btn btn-sm btn-success mb-2 px-1 py-0" wire:click="addRow" style="font-size: 0.8rem;">
            <i class="bi bi-plus-circle"></i> <span>Tambah</span>
        </button>
        <div class="card mb-2 shadow-sm">
            <div class="table-responsive">
                <table class="excel-table">
                    <thead class="table-light text-center align-middle" style="background-color:rgb(121, 173, 246);">
                        <tr>
                            <th rowspan="2" style="width: 50px; text-align: center;">No </th>
                            {{-- Produk --}}
                            @for ($i = 1; $i <= 7; $i++)
                                <th colspan="2">
                                    <select wire:model.live="selectedKategori{{ $i }}"
                                        class="excel-input @error('selectedKategori' . $i) is-invalid @enderror"
                                        style="font-size:.8rem; height:30px; background-color:rgb(121, 173, 246);" wire:change="loadData">
                                        <option value="" class="text-center" style="font-weight: bold;">-- Produk
                                            --</option>
                                        @foreach ($kategori_produk as $produk)
                                            <option value="{{ $produk->kategori_produk_id }}" class="text-center">
                                                {{ $produk->nama_produk }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedKategori' . $i)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </th>
                            @endfor
                            <th rowspan="2" style="width: 40px;">Aksi</th>
                        </tr>
                        <tr>
                            @for ($i = 1; $i <= 7; $i++)
                                <th style="width: 120px;">Berat (Kg)</th>
                                <th style="width: 120px;">Total (Pcs)</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                {{-- Berat & Total --}}
                                @for ($i = 1; $i <= 7; $i++)
                                    <td>
                                        <input type="number" step="0.01"
                                            wire:model.live="rows.{{ $index }}.berat_produk{{ $i }}"
                                            wire:model.defer="rows.{{ $index }}.berat_produk{{ $i }}"
                                            wire:change="calculateTotals" class="excel-input text-center"
                                            placeholder="Kg">
                                    </td>
                                    <td>
                                        <input type="number" step="1"
                                            wire:model.live="rows.{{ $index }}.total_produk{{ $i }}"
                                            wire:model.defer="rows.{{ $index }}.total_produk{{ $i }}"
                                            wire:change="calculateTotals" class="excel-input text-center"
                                            placeholder="Pcs">
                                    </td>
                                @endfor
                                {{-- aksi --}}
                                <td>
                                    <button class="btn btn-danger btn-sm py-0"
                                        wire:click="removeRow({{ $index }})"
                                        style="font-size:.7rem; height:30px; width:30px;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        {{-- Total --}}
                        <tr class="table-secondary fw-bold excel-input text-center"
                            style="background-color:rgb(121, 173, 246);">
                            <td>Total</td>
                            @for ($i = 1; $i <= 7; $i++)
                                <td>{{ number_format($total_berat[$i] ?? 0, 2) }} kg</td>
                                <td>{{ number_format($total_pcs[$i] ?? 0, 0) }} pcs</td>
                            @endfor
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Button Simpan --}}
    <div class="card-footer px-2 py-1 text-end">
        <button type="submit" class="btn btn-primary btn-sm px-2 py-0" wire:click="saveAll"
            wire:loading.attr="disabled" style="font-size:0.7rem; height: 30px;">
            <i class="bi bi-save"></i> <span>Simpan</span>
        </button>

        <button type="button" class="btn btn-secondary btn-sm px-2 py-0" wire:click="print"
            style="font-size:0.7rem; height: 30px;">
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
