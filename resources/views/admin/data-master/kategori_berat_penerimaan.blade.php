@extends('layouts.app')

@section('content')
    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12">
                        <h3>PT BAHARI PRIMA MANUNGGAL</h3>
                        <p class="text-subtitle text-muted">Data Sizing Penerimaan</p>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <h4 class="card-title text-white">Daftar Sizing Penerimaan</h4>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#tambahKategoriBeratModal">
                            <i class="bi bi-plus-circle"></i> Tambah
                        </button>
                    </div>
                    
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="table2">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Sizing</th>
                                        <th class="text-center" style="width: 100px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kategori_berat_penerimaan as $item)
                                    <tr style="background: linear-gradient(to right, #f9f9f9 0%, #f0f7ff 100%);">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->kategori_berat }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                data-bs-target="#editKategoriBeratModal{{ $item->kategori_berat_id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('kategori_berat_penerimaan.destroy', $item->kategori_berat_id) }}" 
                                                method="POST" class="d-inline delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger delete-btn" >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editKategoriBeratModal{{ $item->kategori_berat_id }}" tabindex="-1" role="dialog" 
                                        aria-labelledby="editKategoriBeratModalLabel{{ $item->kategori_berat_id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
                                                    <h5 class="modal-title text-white" id="editKategoriBeratModalLabel{{ $item->kategori_berat_id }}">Edit</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('kategori_berat_penerimaan.update', $item->kategori_berat_id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-3">
                                                            <label for="kategori_berat" class="form-label">Sizing</label>
                                                            <input type="text" class="form-control" id="kategori_berat" 
                                                                name="kategori_berat" value="{{ $item->kategori_berat }}" required>
                                                        </div>
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="tambahKategoriBeratModal" tabindex="-1" role="dialog" 
        aria-labelledby="tambahKategoriBeratModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <h5 class="modal-title text-white" id="tambahKategoriBeratModalTitle">Tambah Sizing Penerimaan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('kategori_berat_penerimaan.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="kategori_berat" class="form-label">Sizing</label>
                            <input type="text" class="form-control" id="kategori_berat" 
                                name="kategori_berat" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
