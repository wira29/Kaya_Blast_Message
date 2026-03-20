@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="mb-0">Tambah Campaign Baru</h3>
            <p>Isi formulir untuk menambahkan campaign baru.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('campaigns.store') }}" method="POST" id="campaignForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="brand_id" class="form-label">Brand <span class="text-danger">*</span></label>
                            <select class="select2 form-control @error('brand_id') is-invalid @enderror"
                                    id="brand_id" name="brand_id" required>
                                <option value="">-- Pilih Brand --</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Campaign <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" placeholder="Masukkan nama campaign"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3"
                              placeholder="Masukkan deskripsi campaign (opsional)">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Affiliates Repeater -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Daftar Afiliator <span class="text-danger">*</span></h5>
                        <button type="button" class="btn btn-sm btn-success" id="addAffiliateBtn">
                            <i class="ti ti-plus"></i> Tambah Afiliator
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="affiliatesTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Nama Afiliator</th>
                                    <th width="30%">Nomor Telepon</th>
                                    <th width="30%">Link Sosmed</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="affiliatesBody">
                                <!-- Dynamic rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                    @error('affiliates')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('affiliates.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check"></i> Simpan Campaign
                    </button>
                    <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
<link href="{{ asset('') }}assets/libs/select2/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
<script src="{{ asset('') }}assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="{{ asset('') }}assets/libs/select2/dist/js/select2.min.js"></script>
<script>
    let affiliateCount = 0;

    // Initialize Select2
    $(document).ready(function() {
        $('#brand_id').select2({
            search: true
        });
    });

    function addAffiliateRow(name = '', phone = '', link = '') {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="text" class="form-control" name="affiliates[${affiliateCount}][name]"
                       placeholder="Nama afiliator" value="${name}" required>
            </td>
            <td>
                <input type="text" class="form-control" name="affiliates[${affiliateCount}][phone]"
                       placeholder="Nomor telepon" value="${phone}" required>
            </td>
            <td>
                <input type="text" class="form-control" name="affiliates[${affiliateCount}][link]"
                       placeholder="Link sosmed" value="${link}">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger removeAffiliateBtn">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        `;

        document.getElementById('affiliatesBody').appendChild(row);

        row.querySelector('.removeAffiliateBtn').addEventListener('click', function() {
            row.remove();
        });

        affiliateCount++;
    }

    document.getElementById('addAffiliateBtn').addEventListener('click', function() {
        addAffiliateRow();
    });

    // Add remove button event for existing rows if needed
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeAffiliateBtn')) {
            e.target.closest('tr').remove();
        }
    });

    // Add at least one row on load
    window.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('affiliatesBody').children.length === 0) {
            addAffiliateRow();
        }
    });

    // Form validation before submit
    document.getElementById('campaignForm').addEventListener('submit', function(e) {
        const affiliatesCount = document.getElementById('affiliatesBody').querySelectorAll('tr').length;
        if (affiliatesCount === 0) {
            e.preventDefault();
            alert('Minimal harus ada 1 afiliator');
            return false;
        }
    });
</script>
@endpush
@endsection
