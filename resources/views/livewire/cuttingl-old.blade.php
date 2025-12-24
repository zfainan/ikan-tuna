<div>
    <!-- Success/Eror Message -->
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
            style="background: linear-gradient(135deg, hsl(210, 97.60%, 48.80%), rgba(209, 202, 0, 0.88)); font-size: 0.85rem;">
            <i class="bi bi-pencil-square me-1"></i>Form Input Data Cutting
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-auto">
                    <label for="session_tggl_cutting" class="form-label small">Tanggal Cutting</label>
                    <input type="date" id="session_tggl_cutting" wire:model.live="session_tggl_cutting"
                        wire:change="updateFilterTanggal('cutting')"
                        class="form-control form-control-sm @error('session_tggl_cutting') is-invalid @enderror"
                        required>
                    @error('session_tggl_cutting')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="session_tggl_injek_co" class="form-label small">Tanggal Injek CO</label>
                    <input type="date" id="session_tggl_injek_co" wire:model.live="session_tggl_injek_co"
                        wire:change="updateFilterTanggal('injek_co')"
                        class="form-control form-control-sm @error('session_tggl_injek_co') is-invalid @enderror"
                        @if (!$session_tggl_cutting) disabled @endif min="{{ $session_tggl_cutting }}" required>
                    @error('session_tggl_injek_co')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="session_tggl_service" class="form-label small">Tanggal Service</label>
                    <input type="date" id="session_tggl_service" wire:model.live="session_tggl_service"
                        wire:change="updateFilterTanggal('service')"
                        class="form-control form-control-sm @error('session_tggl_service') is-invalid @enderror"
                        @if (!$session_tggl_injek_co) disabled @endif min="{{ $session_tggl_injek_co }}" required>
                    @error('session_tggl_service')
                        <div class="invalid-feedback small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-auto">
                    <label for="selectedTanggalPenerimaan" class="form-label small">Tanggal Penerimaan</label>
                    <select id="selectedTanggalPenerimaan" wire:model.live="selectedTanggalPenerimaan"
                        class="form-select form-select-sm @error('selectedTanggalPenerimaan') is-invalid @enderror"
                        @if (!$session_tggl_service) disabled @endif required>
                        <option value="">Tanggal Penerimaan</option>
                        @if (isset($penerimaan_ikan) && $penerimaan_ikan->isNotEmpty())
                            @foreach ($penerimaan_ikan->unique('tgl_penerimaan') as $penerimaan)
                                <option
                                    value="{{ \Carbon\Carbon::parse($penerimaan->tgl_penerimaan)->toDateString() }}">
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
                    <label for="penerimaan_id" class="form-label small">Supplier</label>
                    <select id="penerimaan_id" wire:model.live="penerimaan_id"
                        class="form-select form-select-sm @error('penerimaan_id') is-invalid @enderror"
                        @if (!$session_tggl_service) disabled @endif required>
                        <option value="" style="text-align: center;">Supplier</option>
                        @forelse ($filteredPenerimaan as $penerimaan)
                            @php
                                $supplier = $penerimaan->supplier;
                                $displayText = sprintf(
                                    '%s %s %s',
                                    strtoupper($penerimaan->jenis_penerimaan ?? 'TIDAK ADA JENIS'),
                                    $supplier->alamat ?? 'TIDAK ADA ALAMAT',
                                    strtoupper($supplier->nama_supplier ?? 'TIDAK ADA SUPPLIER'),
                                );
                            @endphp
                            <option value="{{ $penerimaan->penerimaan_id }}" style="text-align: center;">
                                {{ $displayText }}
                            </option>
                        @empty
                            <option value="" style="text-align: center;">Tidak ada data penerimaan ikan</option>
                        @endforelse
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

    {{-- ======== TABEL INPUT DETAIL (berat & pcs) + Tombol Tambah & Simpan ======== --}}
    <div class="d-flex justify-content-center my-3">
        <div class="card-header d-flex justify-content-between align-items-center px-2 py-1" style="max-width: 450px;">
            <span class="fw-semibold"
                style="font-size: 1.3rem; font-family: 'Copperplate', fantasy; color:rgb(16, 10, 10); letter-spacing: 1px; text-transform: uppercase;">
                <img src="/img/Logo.png" alt="Logo" width="100" height="100"> Tally Cutting By Loin</span>
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

                        {{-- Nomor Batch --}}
                        <tr>
                            <th rowspan="4" style="width: 5px;">No</th>
                            <th colspan="9" style="width: 200px;">
                                <input type="text" id="noBatch" wire:model.live.debounce.500ms="noBatch"
                                    wire:change="searchByBatch" wire:key="noBatch_{{ rand() }}"
                                    class="excel-input text-center" placeholder="No Batch"
                                    style="background-color:rgb(121, 173, 246); font-weight: bold; font-size: 0.8rem;"
                                    required>
                                @error('noBatch')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </th>
                            <th rowspan="4" style="width: 5px;">Aksi</th>
                        </tr>
                        <tr>
                            <th colspan="3" style="width: 30px;">Cutting</th>
                            <th colspan="3" style="width: 30px;">Loin RM Service</th>
                            <th colspan="3" style="width: 30px;">Hasil Hasil RM Service</th>
                        </tr>
                        <tr>

                            {{-- Size/Grade --}}
                            @php
                                $selectedSizingLoin = $selectedSizingLoin ?? [''];
                                $sizingLoin = $sizingLoin ?? [];
                            @endphp

                            @for ($i = 0; $i < count($selectedSizingLoin); $i++)
                                <th colspan="3" style="width: 30px;">
                                    <select wire:model="selectedSizingLoin.1"
                                        class="excel-input @error('selectedSizingLoin.1') is-invalid @enderror"
                                        style="font-size: .8rem; height: 30px; background-color:rgb(121, 173, 246);">
                                        <option value="" class="text-center" style="font-weight: bold;">--
                                            Size/Grade --</option>
                                        @foreach ($sizingLoin as $sizing)
                                            <option
                                                value="{{ $sizing['grade_size_id'] ?? ($sizing->grade_size_id ?? '') }}"
                                                class="text-center">
                                                {{ $sizing['grade_sizing'] ?? ($sizing->grade_sizing ?? '') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedSizingLoin.1')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </th>
                            @endfor

                            {{-- Grade Service RM Service --}}
                            @php
                                $selectedGradingService = $selectedGradingService ?? [1 => null, 2 => null, 3 => null];
                                $gradingService = $gradingService ?? [];
                            @endphp

                            @for ($i = 1; $i <= 3; $i++)
                                <th colspan="1" style="width: 30px;">
                                    <select wire:model="selectedGradingService.{{ $i }}"
                                        class="excel-input @error('selectedGradingService.{{ $i }}') is-invalid @enderror"
                                        style="font-size: .8rem; height: 30px; background-color:rgb(121, 173, 246);">
                                        <option value="" class="text-center" style="font-weight: bold;">--
                                            Grade --</option>
                                        @foreach ($gradingService as $gradeService)
                                            <option value="{{ $gradeService->grade_size_id }}" class="text-center">
                                                {{ $gradeService->grading }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedGradingService.' . $i)
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </th>
                            @endfor

                            {{-- Grade Hasil Service --}}
                            @php
                                $selectedGradingHservice = $selectedGradingHservice ?? [
                                    1 => null,
                                    2 => null,
                                    3 => null,
                                ];
                                $gradingHservice = $gradingHservice ?? collect();
                            @endphp

                            @for ($i = 1; $i <= 3; $i++)
                                <th colspan="1" style="width: 30px;">
                                    <select wire:model="selectedGradingHservice.{{ $i }}"
                                        class="excel-input @error('selectedGradingHservice.{{ $i }}') is-invalid @enderror"
                                        style="font-size: .8rem; height: 30px; background-color:rgb(121, 173, 246);">
                                        <option value="" class="text-center" style="font-weight: bold;">--
                                            Grade --</option>
                                        @foreach ($gradingHservice as $gradeHservice)
                                            <option value="{{ $gradeHservice->grade_servicehs_id }}"
                                                class="text-center" @if (($selectedGradingHservice[$i] ?? null) == $gradeHservice->grade_servicehs_id) selected @endif>
                                                {{ $gradeHservice->grade_servicehs }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('selectedGradingHservice.' . $i)
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </th>
                            @endfor

                        </tr>
                        <tr>
                            <th colspan="1" style="width: 30px;">Berat</th>
                            <th colspan="1" style="width: 30px;">Suhu Loin</th>
                            <th colspan="1" style="width: 30px;">No. Loin</th>
                            @for ($i = 1; $i <= 6; $i++)
                                <th colspan="1" style="width: 30px;">Berat</th>
                            @endfor
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($rows as $index => $row)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                {{-- berat Loin --}}
                                <td>
                                    <input type="number" step="0.01"
                                        wire:model.live.debounce.500ms="rows.{{ $index }}.berat_loin"
                                        class="excel-input text-center" placeholder="Kg">
                                </td>

                                {{-- suhu loin --}}
                                <td>
                                    <input type="number" step="0.1"
                                        wire:model.live.debounce.500ms="rows.{{ $index }}.suhu_loin"
                                        class="excel-input text-center" placeholder="°C">
                                </td>

                                {{-- No. Loin --}}
                                <td>
                                    <input type="text"
                                        wire:model.live.debounce.500ms="rows.{{ $index }}.no_loin"
                                        class="excel-input text-center" placeholder="No. Loin">
                                </td>

                                {{-- input berat RM --}}
                                @for ($i = 1; $i <= 3; $i++)
                                    <td>
                                        <input type="number" step="0.01"
                                            wire:model.live="rows.{{ $index }}.berat_{{ $i }}"
                                            class="excel-input text-center" placeholder="0">
                                    </td>
                                @endfor

                                {{-- input berat HS --}}
                                @for ($i = 4; $i <= 6; $i++)
                                    <td>
                                        <input type="number" step="0.01"
                                            wire:model.live="rows.{{ $index }}.berat_{{ $i }}"
                                            class="excel-input text-center" placeholder="0">
                                    </td>
                                @endfor

                                {{-- aksi --}}
                                <td>
                                    <button class="btn btn-danger btn-sm py-0"
                                        wire:click="removeRow({{ $row['cutting_id'] ?? ($index ?? 'null') }})"
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
                            <td>{{ number_format($berat_loin, 2) }} kg</td>
                            <td></td>
                            <td></td>
                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ number_format($berat_rm[$i] ?? 0, 2) }} kg</td>
                            @endfor
                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ number_format($berat_hs[$i] ?? 0, 2) }} kg</td>
                            @endfor
                            <td></td>
                        </tr>
                        {{-- Pcs --}}
                        <tr class="table-secondary fw-bold excel-input text-center"
                            style="background-color:rgb(121, 173, 246);">
                            <td>Pcs</td>
                            <td>{{ $pcs_loin ?? 0 }}</td>
                            <td></td>
                            <td></td>
                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ $pcs_rm[$i] ?? 0 }}</td>
                            @endfor
                            @for ($i = 0; $i < 3; $i++)
                                <td>{{ $pcs_hs[$i] ?? 0 }}</td>
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
        <button type="button" class="btn btn-primary btn-sm px-2 py-0" wire:click.prevent="saveAll"
            wire:loading.attr="disabled" wire:target="saveAll" style="font-size: 0.7rem; height: 30px;">
            <span wire:loading.remove wire:target="saveAll">
                <i class="bi bi-save"></i> <span>Simpan</span>
            </span>
            <span wire:loading wire:target="saveAll">
                <span class="spinner-border spinner-border-sm" role="status"></span>
                Menyimpan...
            </span>
        </button>

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
                        location.reload();
                    }
                });
            });
        });
    </script>
@endpush
