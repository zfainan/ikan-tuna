<div>
    {{-- =========================
        SUCCESS / ERROR MESSAGE
    ========================== --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- =========================
        FORM FILTER SESSION
    ========================== --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white"
            style="background:linear-gradient(135deg,hsl(210,97%,48%),rgba(209,202,0,.88));font-size:.85rem">
            <i class="bi bi-pencil-square me-1"></i> Form Input Data Cutting
        </div>

        <div class="card-body p-3">
            <div class="row g-2">
                {{-- Tanggal Cutting --}}
                <div class="col-md-auto">
                    <label class="form-label small">Tanggal Cutting</label>
                    <input type="date" wire:model.live="session_tggl_cutting" class="form-control form-control-sm"
                        required>
                </div>

                {{-- Tanggal Injek CO --}}
                <div class="col-md-auto">
                    <label class="form-label small">Tanggal Injek CO</label>
                    <input type="date" wire:model.live="session_tggl_injek_co" class="form-control form-control-sm"
                        min="{{ $session_tggl_cutting }}" @disabled(!$session_tggl_cutting) required>
                </div>

                {{-- Tanggal Service --}}
                <div class="col-md-auto">
                    <label class="form-label small">Tanggal Service</label>
                    <input type="date" wire:model.live="session_tggl_service" class="form-control form-control-sm"
                        min="{{ $session_tggl_injek_co }}" @disabled(!$session_tggl_injek_co) required>
                </div>

                {{-- Tanggal Penerimaan --}}
                <div class="col-md-auto">
                    <label class="form-label small">Tanggal Penerimaan</label>
                    <select wire:model.live="selectedTanggalPenerimaan" class="form-select form-select-sm"
                        @disabled(!$session_tggl_service) required>
                        <option value="">Pilih Tanggal</option>
                        @foreach ($penerimaan_ikan->unique('tgl_penerimaan') as $p)
                            <option value="{{ $p->penerimaan_id }}">
                                {{ \Carbon\Carbon::parse($p->tgl_penerimaan)->format('d F Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jenis Penerimaan --}}
                <div class="col-md-auto">
                    <label class="form-label small">Jenis Penerimaan</label>
                    <select wire:model.live="penerimaan_id" class="form-select form-select-sm"
                        @disabled(!$selectedTanggalPenerimaan) required>
                        <option value="">Pilih</option>
                        @foreach ($filteredPenerimaan as $p)
                            <option value="{{ $p->penerimaan_id }}">
                                {{ $p->jenis_penerimaan }}
                                {{ $p->supplier->nama_supplier ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Sesi --}}
    <div class="row mt-3">
        <div class="col-12">
            @if ($session_tggl_cutting && $session_tggl_injek_co && $penerimaan_id)
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
                                <div><strong>Tanggal Cutting:</strong>
                                    {{ \Carbon\Carbon::parse($session_tggl_cutting)->format('d F Y') }}</div>
                                <div><strong>Tanggal Injek CO:</strong>
                                    {{ \Carbon\Carbon::parse($session_tggl_injek_co)->format('d F Y') }}</div>
                            </div>
                            <div class="col-4 text-center">
                                <div><strong>Tanggal Service:</strong>
                                    {{ \Carbon\Carbon::parse($session_tggl_service)->format('d F Y') }}</div>
                                @if ($selectedPenerimaan)
                                    <div><strong>Tanggal Penerimaan:</strong>
                                        {{ \Carbon\Carbon::parse($selectedPenerimaan->tgl_penerimaan)->format('d F Y') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-4 text-end">
                                @if ($selectedPenerimaan)
                                    <div><strong>Jenis Penerimaan:</strong> {{ $selectedPenerimaan->jenis_penerimaan }}
                                    </div>
                                    <div><strong>Supplier:</strong>
                                        {{ $selectedPenerimaan->supplier->nama_supplier ?? 'Tidak ada supplier' }}
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
                    @if (!$session_tggl_cutting)
                        Pilih tanggal cutting terlebih dahulu.
                    @elseif(!$session_tggl_injek_co)
                        Pilih tanggal injek CO terlebih dahulu.
                    @elseif(!$session_tggl_service)
                        Pilih tanggal service terlebih dahulu.
                    @elseif(!$penerimaan_id)
                        Pilih penerimaan untuk melanjutkan input data.
                    @else
                        Lengkapi semua data sesi terlebih dahulu.
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- =========================
        TABLE TITLE
    ========================== --}}
    <div class="my-3 text-center">
        <h5 style="font-family:'Copperplate',fantasy">
            <span class="fw-semibold"
                style="font-size: 1.3rem; font-family: 'Copperplate', fantasy; color:rgb(16, 10, 10); letter-spacing: 1px; text-transform: uppercase;">
                <img src="/img/Logo.png" alt="Logo" width="100" height="100">
                Tally Cutting By Loin
            </span>
        </h5>
    </div>

    <div class="card-body p-1">
        {{-- =========================
            BUTTON ADD ROW
        ========================== --}}
        <button class="btn btn-sm btn-success mb-2 px-1 py-0" wire:click="addRow" style="font-size: 0.8rem;">
            <i class="bi bi-plus-circle"></i> Tambah
        </button>

        {{-- =========================
            TABLE
        ========================== --}}
        <div class="card mb-2 shadow-sm">
            <div class="table-responsive">
                <table class="excel-table">
                    <thead class="table-light text-center align-middle" style="background-color:rgb(121, 173, 246);">
                        <tr>
                            <th rowspan="5">No</th>
                            <th colspan="9">
                                <input type="text" id="no_batch" wire:model.live="noBatch" wire:change="loadData"
                                    class="excel-input text-center"
                                    placeholder="No Batch"
                                    style="background-color:rgb(121, 173, 246); font-weight: bold; font-size: 0.8rem;"
                                    required>
                                @error('no_batch')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </th>
                            <th rowspan="5">Aksi</th>
                        </tr>

                        <tr>
                            <th colspan="3">Loin</th>
                            <th colspan="3">Loin RM Service</th>
                            <th colspan="3">Hasil Loin RM Service</th>
                        </tr>

                        {{-- HEADER GRADE --}}
                        <tr>
                            <th colspan="3">
                                <select wire:model.live="selectedSizingLoin"
                                    style="font-size: .8rem; height: 30px; background-color:rgb(121, 173, 246);"
                                    class="excel-input"
                                    wire:change="loadDataByHeader">
                                    <option value="{{ null }}" class="text-center" style="font-weight: bold;">
                                        -- Size/Grade --
                                    </option>
                                    @foreach ($sizingLoin as $s)
                                        <option class="text-center" value="{{ $s->grade_size_id }}">
                                            {{ $s->grade_sizing }}
                                        </option>
                                    @endforeach
                                </select>
                            </th>

                            @for ($i = 1; $i <= 3; $i++)
                                <th>
                                    <select wire:model.live="selectedGradingService{{ $i }}"
                                        style="font-size: .8rem; height: 30px; background-color:rgb(121, 173, 246);"
                                        class="excel-input">
                                        <option value="{{ null }}" class="text-center" style="font-weight: bold;">-- Grade RM --</option>
                                        @foreach ($gradingService as $g)
                                            <option value="{{ $g->grade_service_id }}" class="text-center">
                                                {{ $g->grading }}
                                            </option>
                                        @endforeach
                                    </select>
                                </th>
                            @endfor

                            @for ($i = 1; $i <= 3; $i++)
                                <th>
                                    <select
                                        wire:model.live="selectedGradingHService{{ $i }}"
                                        style="font-size: .8rem; height: 30px; background-color:rgb(121, 173, 246);"
                                        class="excel-input">
                                        <option value="{{ null }}" class="text-center" style="font-weight: bold;">-- Grade HS --</option>
                                        @foreach ($gradingHservice as $g)
                                            <option value="{{ $g->grade_servicehs_id }}" class="text-center">{{ $g->grade_servicehs }}</option>
                                        @endforeach
                                    </select>
                                </th>
                            @endfor
                        </tr>

                        <tr>
                            <th>Berat</th>
                            <th>Suhu</th>
                            <th>No Loin</th>
                            @for ($i = 1; $i <= 6; $i++)
                                <th>Berat</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>

                                {{-- LOIN --}}
                                <td>
                                    <input type="number" step="0.01"
                                        wire:model.live="rows.{{ $index }}.berat_loin"
                                        class="excel-input text-center">
                                </td>
                                <td>
                                    <input type="number" step="0.1"
                                        wire:model.live="rows.{{ $index }}.suhu_loin"
                                        class="excel-input text-center">
                                </td>
                                <td>
                                    <input type="text" wire:model.live="rows.{{ $index }}.no_loin"
                                        class="excel-input text-center">
                                </td>

                                {{-- RM --}}
                                @for ($i = 1; $i <= 3; $i++)
                                    <td>
                                        <input type="number" step="0.01"
                                            wire:model.live="rows.{{ $index }}.rm_berat_{{ $i }}"
                                            class="excel-input text-center">
                                    </td>
                                @endfor

                                {{-- HS --}}
                                @for ($i = 1; $i <= 3; $i++)
                                    <td>
                                        <input type="number" step="0.01"
                                            wire:model.live="rows.{{ $index }}.hs_berat_{{ $i }}"
                                            class="excel-input text-center">
                                    </td>
                                @endfor

                                {{-- AKSI --}}
                                <td class="text-center">
                                    <button class="btn btn-danger btn-sm"
                                        wire:click="removeRow({{ $index }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- FOOTER TOTAL --}}
                    <tfoot class="fw-bold text-center" style="background:#79adf6">
                        <tr>
                            <td>Total</td>
                            <td>{{ number_format($berat_loin, 2) }}</td>
                            <td></td>
                            <td></td>

                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ number_format($berat_rm[$i], 2) }}</td>
                            @endfor
                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ number_format($berat_hs[$i], 2) }}</td>
                            @endfor
                            <td></td>
                        </tr>

                        <tr>
                            <td>Pcs</td>
                            <td>{{ $pcs_loin }}</td>
                            <td></td>
                            <td></td>
                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ $pcs_rm[$i] }}</td>
                            @endfor
                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ $pcs_hs[$i] }}</td>
                            @endfor
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- =========================
        SAVE BUTTON
    ========================== --}}
    <div class="mt-3 text-end">
        <button class="btn btn-primary btn-sm" wire:click="saveAll" wire:loading.attr="disabled">
            <i class="bi bi-save"></i> Simpan
        </button>
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
                        location.reload();
                    }
                });
            });
        });
    </script>
@endpush
