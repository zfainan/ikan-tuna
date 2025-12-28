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
                        <h3>Data Cutting Loin RM</h3>
                        <p class="text-subtitle text-muted">Silahkan kelola data cutting loin</p>
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
                <div class="modal fade" id="tambahCuttingLModal"
                    tabindex="-1" role="dialog"
                    aria-labelledby="tambahCuttingLModalTitle"
                    aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                            <div class="modal-content"></div>
                        </div>
                </div>
                <div class="card-body">
                    @livewire('cutting-by-l')
                </div>
            </section>
        </div>
    </div>
@endsection
