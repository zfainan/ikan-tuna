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
                    <h3>Data Penerimaan Ikan</h3>
                    <p class="text-subtitle text-muted">Silahkan kelola data penerimaan ikan</p>
                </div>

                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.html" style="font-weight: bold;">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page" style="font-weight: bold;">{{ Request::segment(1) }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <!-- Modal untuk Tambah Penerimaan (Supplier + Date) -->
            <div class="modal fade" id="tambahPenerimaanModal" tabindex="-1" role="dialog"
                aria-labelledby="tambahPenerimaanModalTitle" aria-hidden="true">
            </div>
            <div class="card-body">
                @livewire('penerimaan-ikan')
            </div>
        </section>
    </div>
</div>
@endsection
