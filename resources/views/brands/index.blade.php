@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Daftar Brand</h3>
            <p>Lihat daftar brand yang terdaftar di platform.</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                <i class="ti ti-plus"></i> Tambah Brand
            </button>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Sukses!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <!-- Search Filter -->
            <div class="mb-4">
                <form action="{{ route('brands.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="search" placeholder="Cari nama brand..."
                               value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-secondary">
                            <i class="ti ti-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="">
                        <tr>
                            <th width="10%">#</th>
                            <th width="50%">Nama Brand</th>
                            <th width="20%">Jumlah Campaign</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($brands as $key => $brand)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $brand->name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $brand->campaigns_count }} Campaign</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                            data-bs-target="#editBrandModal"
                                            onclick="editBrand({{ $brand->id }})">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#deleteBrandModal"
                                            onclick="deleteBrand({{ $brand->id }}, '{{ $brand->name }}')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <p class="text-muted mb-0">Tidak ada data brand</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $brands->links() }}
        </div>
    </div>
</div>

<!-- Add Brand Modal -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBrandModalLabel">Tambah Brand Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('brands.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="brandName" class="form-label">Nama Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="brandName" name="name" placeholder="Masukkan nama brand"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Brand Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" aria-labelledby="editBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBrandModalLabel">Edit Brand</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editBrandName" class="form-label">Nama Brand <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editBrandName" name="name"
                               placeholder="Masukkan nama brand" required>
                        <div class="invalid-feedback" id="editNameError"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Brand Modal -->
<div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-labelledby="deleteBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger" id="deleteBrandModalLabel">
                    <i class="ti ti-alert-triangle"></i> Hapus Brand
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus brand <strong id="deleteBrandName"></strong>?</p>
                    <p class="text-muted small mb-0">Catatan: Brand dengan campaign tidak dapat dihapus.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus Brand</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    function editBrand(brandId) {
        fetch(`/brands/${brandId}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editBrandName').value = data.name;
                document.getElementById('editForm').action = `/brands/${brandId}`;
            })
            .catch(error => console.error('Error:', error));
    }

    function deleteBrand(brandId, brandName) {
        document.getElementById('deleteBrandName').textContent = brandName;
        document.getElementById('deleteForm').action = `/brands/${brandId}`;
    }
</script>
@endpush

@endsection
