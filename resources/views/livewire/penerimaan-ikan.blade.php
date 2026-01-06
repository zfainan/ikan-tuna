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
    <div class="card shadow-sm border-0">
        <div class="card-header py-2 px-3 text-white"
             style="background: linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); font-size: 0.85rem;">
            <i class="bi bi-pencil-square me-1"></i>Form Input Data Penerimaan Ikan
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-auto">
                    <label for="session_date" class="form-label small">Tanggal Penerimaan</label>
                        <input type="date" id="session_date" 
                            wire:model.live="session_date" 
                            class="form-control form-control-sm @error('session_date') is-invalid @enderror" 
                            required>
                        @error('session_date')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                </div>

                <div class="col-md-auto">
                    <label for="session_tgl_bongkar" class="form-label small">Tanggal Bongkar</label>
                        <input type="date" id="session_tgl_bongkar" 
                            wire:model.live="session_tgl_bongkar" 
                            class="form-control form-control-sm @error('session_tgl_bongkar') is-invalid @enderror"
                            @if(!$session_date) disabled @endif 
                            min="{{ $session_date }}"
                            required>
                    @error('session_tgl_bongkar')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="session_supplier" class="form-label small">Supplier</label>
                        <select id="session_supplier" 
                            wire:model.live="session_supplier" 
                            class="form-select form-select-sm @error('session_supplier') is-invalid @enderror"
                            @if(!$session_date || !$session_tgl_bongkar) disabled @endif 
                            required>
                        <option value="">Pilih Supplier</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->supplier_id }}">
                                {{ $supplier->nama_supplier }}
                            </option>
                        @endforeach
                    </select>
                    @error('session_supplier')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="session_jenis_penerimaan" class="form-label small">Jenis Penerimaan</label>
                        <select id="session_jenis_penerimaan"
                            wire:model.live="session_jenis_penerimaan"
                            class="form-select form-select-sm @error('session_jenis_penerimaan') is-invalid @enderror"
                            @if(!$session_date || !$session_tgl_bongkar || !$session_supplier) disabled @endif 
                            required>
                            <option value="Pilih Jenis Penerimaan">Jenis Penerimaan</option>
                            <option value="Fresh GG">Fresh GG</option>
                            <option value="Frozen GG BE">Frozen GG BE</option>
                            <option value="Frozen GG BF">Frozen GG BF</option>
                            <option value="Frozen GG YF">Frozen GG YF</option>
                            <option value="Frozen WR BE">Frozen WR BE</option>
                            <option value="Frozen WR BF">Frozen WR BF</option>
                            <option value="Frozen WR YF">Frozen WR YF</option>
                    </select>
                    @error('session_jenis_penerimaan')
                        <div class="invalid-feedback small d-block">
                            <i class="bi bi-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-1">
                    <label for="session_no_bak" class="form-label small">No Bak</label>
                        <input type="text" id="session_no_bak" 
                            wire:model.live="session_no_bak" 
                            class="form-control form-control-sm @error('session_no_bak') is-invalid @enderror"
                            @if(!$session_date || !$session_tgl_bongkar || !$session_supplier || !$session_jenis_penerimaan) disabled @endif 
                            required>
                    @error('session_no_bak')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Status Sesi --}}
    <div class="row mt-3">
        <div class="col-12">
            @if($session_date && $session_tgl_bongkar && $session_supplier && $session_jenis_penerimaan && $session_no_bak)
                @php    
                    $selectedSupplier = $suppliers->firstWhere('supplier_id', $session_supplier);
                @endphp
                <div class="p-2 rounded-3 shadow-sm text-white"
                    style="background: linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); font-size: 0.75rem;">
                    <i class="bi bi-check-circle-fill me-1"></i> 
                    <strong>Sesi Aktif:</strong>
                    <div class="mt-1">
                        <div class="row">
                            <div class="col-4 text-start">
                                <div><strong>Tanggal Penerimaan:</strong> {{ \Carbon\Carbon::parse($session_date)->format('d F Y') }}</div>
                                <div><strong>Tanggal Bongkar:</strong> {{ \Carbon\Carbon::parse($session_tgl_bongkar)->format('d F Y') }}</div>
                            </div>
                            <div class="col-4 text-center">
                                <div><strong>Supplier:</strong> {{ $selectedSupplier ? $selectedSupplier->nama_supplier : 'Unknown' }}</div>
                                <div><strong>Jenis Penerimaan:</strong> {{ $session_jenis_penerimaan }}</div>
                            </div>
                            <div class="col-4 text-end">
                                <div><strong>No. Bak:</strong> {{ $session_no_bak }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-2 rounded-3 shadow-sm text-white"
                    style="background:linear-gradient(135deg,hsl(210, 97.60%, 48.80%),rgba(209, 202, 0, 0.88)); border: 1px solid rgb(255, 255, 255); font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i> 
                    @if(!$session_date)
                        Pilih tanggal penerimaan terlebih dahulu.
                    @elseif(!$session_tgl_bongkar)
                        Pilih tanggal bongkar terlebih dahulu.
                    @elseif(!$session_supplier)
                        Pilih supplier untuk melanjutkan input data.
                    @elseif(!$session_jenis_penerimaan)
                        Pilih jenis penerimaan untuk melanjutkan input data.
                    @elseif(!$session_no_bak)
                        Pilih no. bak untuk melanjutkan input data.
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
            text-align: center;
            height: 28px;
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
    
    {{-- ======== TABEL INPUT DETAIL (berat & suhu) + Tombol Tambah & Simpan ======== --}}
    <div class="d-flex justify-content-center my-3">
        <div class="card-header d-flex justify-content-between align-items-center py-1 px-2" style="max-width: 500px;">
            <span class="fw-semibold" style="font-size: 1.3rem; font-family: 'Copperplate', fantasy; color:rgb(16, 10, 10); letter-spacing: 1px; text-transform: uppercase;">
                <img src="/img/Logo.png" alt="Logo" width="100" height="100"> Tally Penerimaan Ikan Tuna</span>
        </div>
    </div>

    <div class="card-body p-1">
        <button class="btn btn-sm btn-success py-0 px-1 mb-2" wire:click="addRow" style="font-size: 0.8rem;">
            <i class="bi bi-plus-circle"></i> <span>Tambah</span>
        </button>
        <div class="card shadow-sm mb-2">
            <div class="table-responsive">
                <table class="excel-table">
                    <thead class="table-light text-center align-middle" style="background-color:rgb(121, 173, 246);">
                        <tr>
                        {{-- No Bak dan Aksi menempel ke bawah --}}
                            <th rowspan="2" style="width: 100px;">No. Bak</th>

                        {{-- Grade di atas --}}
                            <th colspan="3">
                                <select wire:model.live="selected_grade_id" 
                                        class="form-select @error('selected_grade_id') is-invalid @enderror" 
                                        @if(!$session_date || !$session_tgl_bongkar || !$session_supplier || !$session_jenis_penerimaan || !$session_no_bak) disabled @endif 
                                        required style="font-size:.8rem; height:30px; background-color:rgb(121, 173, 246);">
                                    <option value="" class="text-center" style="font-weight: bold;">-- Grade/Size --</option>
                                    @foreach($grades as $grade)
                                        @foreach($kategori_berat as $kategori)
                                            <option value="{{ $grade->grade_id }}_{{ $kategori->kategori_berat_id }}" class="text-center" style="font-weight: bold;">
                                                {{ $grade->grade }} - {{ $kategori->kategori_berat }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </th>
                            <th rowspan="2" style="width: 80px;">Aksi</th>
                        </tr>
                        <tr>
                            <th style="width: 120px;">Berat (Kg)</th>
                            <th style="width: 120px;">Suhu (°C)</th>
                            <th style="width: 120px;">No Ikan</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $rowsCollection = collect($rows ?? []);
                            $total_berat = $rowsCollection->sum(fn($r) => (float)($r['berat_ikan'] ?? 0));
                            $total_ekor  = $rowsCollection->count();
                        @endphp

                        @if(isset($rows) && count($rows) > 0)
                            @foreach($rows as $index => $row)
                                <tr>
                                {{-- No. Bak --}}
                                    <td class="text-center align-middle">
                                        {{ $session_no_bak }}
                                    </td>

                                {{-- Berat --}}
                                <td>
                                    <input type="number" step="0.01" 
                                            wire:model="rows.{{ $index }}.berat_ikan"
                                            class="excel-input text-center"
                                            placeholder="Kg"
                                            required>
                                                @error('rows.{{ $index }}.berat_ikan')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                </td>

                                {{-- Suhu --}}
                                <td>
                                    <input type="number" step="0.1" 
                                            wire:model="rows.{{ $index }}.suhu_ikan"
                                            class="excel-input text-center"
                                            placeholder="°C"
                                            required>
                                                @error('rows.{{ $index }}.suhu_ikan')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                </td>

                                {{-- No Ikan --}}
                                <td>
                                    <input type="number" step="1" 
                                            wire:model="rows.{{ $index }}.no_ikan"
                                            class="excel-input text-center"
                                            placeholder="No Ikan"
                                            required>
                                                @error('rows.{{ $index }}.no_ikan')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <button class="btn btn-danger btn-sm py-0"
                                            wire:click="removeRow({{ $index }})"
                                            style="font-size:.7rem; height:30px; width:30px;">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                </td>
                                </tr>
                                @endforeach
                                @endif

                                @if(isset($rows) && count($rows) > 0)
                                    <tr class="table-secondary fw-bold excel-input text-center" style="background-color:rgb(121, 173, 246);">
                                        <td>Total</td>
                                        <td>{{ number_format($total_berat, 2) }} kg</td>
                                        <td>{{ $total_ekor }} ekor</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Button Simpan --}}
    <div class="card-footer text-end py-1 px-2">
        <button type="button" 
                class="btn btn-primary btn-sm py-0 px-2" 
                    wire:click.prevent="saveAll" 
                    wire:loading.attr="disabled"
                    style="font-size: 0.7rem; height: 30px;">
                <span wire:loading.remove wire:target="saveAll">
                    <i class="bi bi-save"></i> Simpan</span>
                <span wire:loading wire:target="saveAll">
                    <span class="spinner-border spinner-border-sm" role="status"></span> 
                    Menyimpan...</span>
        </button>
    </div>


    {{-- ALERT PESAN --}}
    @if (session()->has('message'))
        <div class="alert alert-success mt-3">
            {{ session('message') }}
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('closeModal', () => {
                // Close the add data modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('tambahDataModal'));
                if (modal) {
                    modal.hide();
                }
            });
        });
    </script>

</div>
