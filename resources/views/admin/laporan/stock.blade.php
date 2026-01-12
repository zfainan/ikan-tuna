@extends('layouts.app')

@section('content')
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>

        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>Laporan Stock</h3>
                    </div>

                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-lg-end float-start">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html" style="font-weight: bold;">Dashboard</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page" style="font-weight: bold;">
                                    {{ Request::segment(1) }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('reports.stock.print') }}" method="POST" target="_blank" class="mt-3">
                    @csrf
                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-12 col-md-2">
                            <label for="since" class="col-form-label">Sejak:</label>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <input type="date" id="since" name="since" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-12 col-md-2">
                            <label for="until" class="col-form-label">Sampai:</label>
                        </div>
                        <div class="col-12 col-md-6 col-lg-3">
                            <input type="date" id="until" name="until" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-12 col-md-2">
                        </div>
                        <div class="col-12 col-md-6 col-lg-3 d-flex gap-3">
                            <a id="show-button" class="btn btn-primary">Tampilkan</a>
                            @if ($data->count() > 0)
                                <button type="submit" class="btn btn-primary">Cetak Laporan</button>
                            @endif
                        </div>
                    </div>
                </form>

                @if ($data->count() > 0)
                    <!-- Main Table -->
                    <table class="table w-full border">
                        <thead>
                            <tr class="thead-bg">
                                <th rowspan="2" class="col-bak">No.</th>
                                <th rowspan="2" class="col-tgl">Produk</th>
                                <th colspan="2" class="col-berat text-center">Packing Masuk</th>
                            </tr>
                            <tr class="thead-bg">
                                <th class="col-bak text-center">Berat (Kg)</th>
                                <th class="col-tgl text-center">Total (Pcs)</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse (($data ?? []) as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row['product'] }}</td>
                                    <td class="text-center">{{ $row['total_berat'] ?? '-' }}</td>
                                    <td class="text-center">{{ $row['total_pcs'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center" style="padding: 10px;">
                                        Data tidak tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                        <tfoot>
                            <tr class="total-row">
                                <td class="font-bold">Total</td>
                                <td></td>
                                <td class="font-bold text-center">
                                    {{ number_format($data->sum('total_berat') ?? 0, 2) }} kg
                                </td>
                                <td class="font-bold text-center">
                                    {{ $data->sum('total_pcs') }} ekor
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sinceInput = document.getElementById('since');
                const untilInput = document.getElementById('until');
                const showButton = document.getElementById('show-button');

                const params = new URLSearchParams(window.location.search);

                // 🔹 Auto-fill dari query param saat halaman load
                if (params.has('since')) {
                    sinceInput.value = params.get('since');
                }
                if (params.has('until')) {
                    untilInput.value = params.get('until');
                }

                function updateHref() {
                    const since = sinceInput.value;
                    const until = untilInput.value;

                    if (!since || !until) {
                        showButton.removeAttribute('href');
                        return;
                    }

                    const url = new URL(window.location.href);
                    url.searchParams.set('since', since);
                    url.searchParams.set('until', until);

                    showButton.href = url.toString();
                }

                // 🔹 Update href setiap input berubah
                sinceInput.addEventListener('change', updateHref);
                untilInput.addEventListener('change', updateHref);

                // 🔹 Set href awal jika query sudah lengkap
                updateHref();
            });
        </script>

    </div>
@endsection
