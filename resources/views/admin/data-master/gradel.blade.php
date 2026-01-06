@extends('layouts.app')

@section('content')
    <div id="main">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="col-12 col-md-6 order-md-1 order-last">
                        <h3>PT BAHARI PRIMA MANUNGGAL</h3>
                        <p class="text-subtitle text-muted">Data Grade/Sizing Loin</p>
                    </div>
                </div>
            </div>

            <section class="section">
                <div class="card" style="border-radius: 10px; overflow: hidden;">
                    <div class="card-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                        <h4 class="card-title text-white">Daftar Grade/Sizing Loin</h4>
                        <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#tambahGradeModal">
                            <i class="bi bi-plus-lg"></i> Tambah
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table1">
                                <thead class="bg-light"> 
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Grade/Sizing</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gradeL as $key => $grade)
                                    <tr style="background: linear-gradient(to right, #f9f9f9 0%, #f0f7ff 100%);">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $grade->grade_sizing }}</td>
                                        <td class="text-center" style="width: 100px;">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                                    data-bs-target="#editGradeModal{{ $grade->grade_size_id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('gradel.destroy', $grade->grade_size_id) }}" 
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Yakin ingin menghapus?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editGradeModal{{ $grade->grade_size_id }}" tabindex="-1" role="dialog" 
                                        aria-labelledby="editGradeModalLabel{{ $grade->grade_size_id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
                                                    <h5 class="modal-title text-white" id="editGradeModalLabel{{ $grade->grade_size_id }}">Edit Grade/Sizing Loin</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{ route('gradel.update', $grade->grade_size_id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="grade_sizing_edit{{ $grade->grade_size_id }}" class="form-label">Grade/Sizing</label>
                                                                <input type="text" class="form-control" id="grade_sizing_edit{{ $grade->grade_size_id }}" 
                                                                    name="grade_sizing" value="{{ $grade->grade_sizing }}" required>
                                                            </div>
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
    <div class="modal fade" id="tambahGradeModal" tabindex="-1" role="dialog"
        aria-labelledby="tambahGradeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                    <h5 class="modal-title text-white" id="tambahGradeModalLabel">Tambah Grade/Sizing Loin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('gradel.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="grade_sizing" class="form-label">Grade/Sizing</label>
                            <input type="text" class="form-control" id="grade_sizing" name="grade_sizing" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
