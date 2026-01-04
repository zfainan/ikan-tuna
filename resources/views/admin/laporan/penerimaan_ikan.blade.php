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
                        <h3>Laporan Penerimaan Ikan</h3>
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
                <form action="{{ route('reports.penerimaan_ikan.print') }}" method="POST" target="_blank" class="mt-3">
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
                        <div class="col-12 col-md-6 col-lg-3">
                        <button type="submit" class="btn btn-primary">Cetak Laporan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
