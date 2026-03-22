@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <!-- Hidden CSRF Token -->
    <input type="hidden" id="csrfToken" value="{{ csrf_token() }}">

    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Insight Sosmed</h3>
            <p>Lihat insight sosmed untuk setiap brand dan unduh data untuk analisis.</p>
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
                <form action="{{ route('insights.index') }}" method="GET" class="row g-3">
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
                    <thead>
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
                                    @if($brand->campaigns_count > 0)
                                        <button class="btn btn-sm btn-primary" onclick="downloadInsight({{ $brand->id }}, '{{ $brand->name }}')" title="Download Insight">
                                            <i class="ti ti-download"></i> Download
                                        </button>
                                    @else
                                        <span class="text-muted">Tidak ada campaign</span>
                                    @endif
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

<script>
function downloadInsight(brandId, brandName) {
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    const csrfToken = document.getElementById('csrfToken').value;

    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="ti ti-loader mr-2"></i> Loading...';

    fetch(`/insights/${brandId}/download`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(async response => {
        // Check if response is successful
        if (response.ok) {
            // Get the file from response
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');

            // Extract filename from Content-Disposition header or use default
            const filename = response.headers
                .get('content-disposition')
                ?.split('filename="')[1]
                ?.split('"')[0] || `insight_${brandId}_${new Date().getTime()}.xlsx`;

            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);

            alert('✓ File insight berhasil diunduh: ' + filename);
        } else {
            // If error, try to parse JSON response
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await response.json();
                let errorMsg = data.message;
                if (data.details) {
                    console.error('Error Details:', data.details);
                    errorMsg += '\n\n📝 Detail Error:\n';
                    if (data.details.status_code) {
                        errorMsg += '• Status Code: ' + data.details.status_code + '\n';
                    }
                    if (data.details.error_message) {
                        errorMsg += '• Server Message: ' + data.details.error_message + '\n';
                    }
                    errorMsg += '\nLihat console untuk detail lengkap.';
                }
                alert('✗ ' + errorMsg);
            } else {
                alert('✗ Terjadi kesalahan saat mengunduh insight\nStatus: ' + response.status + ' ' + response.statusText);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('✗ Terjadi kesalahan saat mengunduh insight\n\nError: ' + error.message);
    })
    .finally(() => {
        // Restore button state
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}
</script>

@endsection
