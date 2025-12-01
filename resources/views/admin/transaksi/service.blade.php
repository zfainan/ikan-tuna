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
                        <h3>Data Service by Produk</h3>
                        <p class="text-subtitle text-muted">Silahkan kelola data service by produk</p>
                    </div>

                    <div class="col-12 col-md-6 order-md-2 order-first">
                        <nav aria-label="breadcrumb" class="breadcrumb-header float-lg-end float-start">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ Request::segment(1) }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="modal fade" id="tambahServiceModal" tabindex="-1" role="dialog"
                    aria-labelledby="tambahServiceModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                        <div class="modal-content"></div>
                    </div>
                </div>
                <div class="card-body">
                    @livewire('service-by-p')
                </div>
            </section>

            <section class="section mt-4">
                <div class="card" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <h4 class="card-title text-white mt-2">Daftar Service by Product</h4>
                    </div>

                    <div class="card-body mt-2">
                        <div class="table-responsive">
                            <table class="table-hover table" id="table2">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nomor Batch</th>
                                        <th>Berat Produk</th>
                                        <th>Total Produk</th>
                                        <th>By Produk</th>
                                        <th>Tanggal Penerimaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($services as $item)
                                        <tr style="background: linear-gradient(to right, #f9f9f9 0%, #f0f7ff 100%);">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->no_batch }}</td>
                                            <td>{{ $item->berat_produk[0] }}</td>
                                            <td>{{ $item->total_produk[0] }}</td>
                                            <td>{{ $item->kategori_byproduk?->nama_produk }}</td>
                                            <td>{{ $item->penerimaan?->tgl_penerimaan
                                                ? \Carbon\Carbon::parse($item->penerimaan->tgl_penerimaan)->format('d F Y')
                                                : 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
