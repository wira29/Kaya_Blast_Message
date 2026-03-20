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
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importAffiliateModal">
                <i class="ti ti-upload"></i> Import Affiliate
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

<!-- Import Affiliate Modal -->
<div class="modal fade" id="importAffiliateModal" tabindex="-1" aria-labelledby="importAffiliateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importAffiliateModalLabel">Import Affiliate dari Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="campaignSelect" class="form-label">Pilih Campaign</label>
                        <select class="form-select" id="campaignSelect" name="campaign_id" required>
                            <option value="">-- Pilih Campaign --</option>
                            @foreach($campaigns as $campaign)
                                <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="excelFile" class="form-label">File Excel</label>
                        <input type="file" class="form-control" id="excelFile" name="excel_file" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Format: .xlsx, .xls, atau .csv dengan kolom: Nama dan Telepon</small>
                    </div>

                    <div class="alert alert-info mb-3">
                        <h6 class="alert-heading">Format File Excel:</h6>
                        <p class="mb-2">File harus memiliki 2 kolom dengan header:</p>
                        <ul class="mb-0">
                            <li><strong>Nama</strong> - Nama affiliate</li>
                            <li><strong>Telepon</strong> - Nomor telepon (format: 62812345678)</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <a href="{{ route('campaigns.download-template') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="ti ti-download"></i> Download Template Excel
                        </a>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitImport">
                    <i class="ti ti-upload"></i> Import
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('submitImport').addEventListener('click', function() {
    const form = document.getElementById('importForm');
    const formData = new FormData(form);
    const campaignId = document.getElementById('campaignSelect').value;
    const excelFile = document.getElementById('excelFile').files[0];

    if (!campaignId) {
        alert('Silakan pilih campaign terlebih dahulu');
        return;
    }

    if (!excelFile) {
        alert('Silakan pilih file Excel terlebih dahulu');
        return;
    }

    const submitBtn = document.getElementById('submitImport');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="ti ti-loader"></i> Sedang mengimport...';

    fetch('{{ route("campaigns.import-affiliate") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Berhasil mengimport ' + data.count + ' affiliate!');
            document.getElementById('importAffiliateModal').querySelector('[data-bs-dismiss="modal"]').click();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Terjadi kesalahan saat import'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim file');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="ti ti-upload"></i> Import';
    });
});

// Reset form when modal is closed
document.getElementById('importAffiliateModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('importForm').reset();
});
</script>
@endsection
