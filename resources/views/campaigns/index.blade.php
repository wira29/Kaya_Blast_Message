@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Daftar Campaign</h3>
            <p>Lihat daftar campaign yang terdaftar di platform.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('campaigns.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Tambah Campaign
            </a>
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
                <form action="{{ route('campaigns.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" placeholder="Cari nama campaign atau brand..."
                               value="{{ $search ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="brand_id">
                            <option value="">-- Semua Brand --</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ ($brandFilter ?? '') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary">
                            <i class="ti ti-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="8%">#</th>
                            <th width="25%">Nama Campaign</th>
                            <th width="32%">Deskripsi</th>
                            <th width="18%">Jumlah Affiliate</th>
                            <th width="17%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campaigns as $key => $campaign)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $campaign->name }}</strong>
                                    <br>
                                    <small class="text-muted">Brand: {{ $campaign->brand->name }}</small>
                                </td>
                                <td>{{ \Str::limit($campaign->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $campaign->affiliates_count }} Affiliate</span>
                                </td>
                                <td>
                                    <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-sm btn-warning">
                                        <i class="ti ti-pencil"></i>
                                    </a>
                                    <form action="{{ route('campaigns.destroy', $campaign->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus campaign ini?')">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <p class="text-muted mb-0">Tidak ada data campaign</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $campaigns->links() }}
        </div>
    </div>
</div>
@endsection
