@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Pengaturan WhatsApp Number Key</h3>
            <p>Atur multiple number key untuk distribusi pesan yang lebih baik.</p>
        </div>
        <div class="col-md-4 text-end">
            <button id="btn-save" class="btn btn-primary">
                Simpan Perubahan
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

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-info mb-4" role="alert">
                    <h6 class="alert-heading"><i class="ti ti-info-circle"></i> Cara Kerja</h6>
                    <p class="mb-2">Sistem akan berganti number key setiap kali mencapai jumlah pesan yang sudah ditentukan:</p>
                    <ul class="mb-0">
                        <li><strong>Contoh dengan 50 pesan:</strong></li>
                        <li><strong>Pesan 0-49:</strong> Menggunakan Number Key 1</li>
                        <li><strong>Pesan 50-99:</strong> Menggunakan Number Key 2</li>
                        <li><strong>Pesan 100-149:</strong> Menggunakan Number Key 3</li>
                        <li><strong>Pesan 150+:</strong> Kembali ke Number Key 1 (berputar)</li>
                    </ul>
                </div>

                <form action="{{ route('settings.number-key.update') }}" id="form" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="number_key_1" class="form-label">Number Key 1</label>
                        <input
                            type="text"
                            class="form-control @error('number_key_1') is-invalid @enderror"
                            id="number_key_1"
                            name="number_key_1"
                            value="{{ old('number_key_1', config('services.watzap.number_key')) }}"
                            placeholder="Masukkan Number Key 1"
                        >
                        @error('number_key_1')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Kunci WhatsApp pertama untuk mengirim pesan</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="number_key_2" class="form-label">Number Key 2 (Opsional)</label>
                        <input
                            type="text"
                            class="form-control @error('number_key_2') is-invalid @enderror"
                            id="number_key_2"
                            name="number_key_2"
                            value="{{ old('number_key_2', config('services.watzap.number_key_2')) }}"
                            placeholder="Masukkan Number Key 2"
                        >
                        @error('number_key_2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Kunci WhatsApp kedua (tidak wajib, jika kosong hanya Key 1 yang digunakan)</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="number_key_3" class="form-label">Number Key 3 (Opsional)</label>
                        <input
                            type="text"
                            class="form-control @error('number_key_3') is-invalid @enderror"
                            id="number_key_3"
                            name="number_key_3"
                            value="{{ old('number_key_3', config('services.watzap.number_key_3')) }}"
                            placeholder="Masukkan Number Key 3"
                        >
                        @error('number_key_3')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Kunci WhatsApp ketiga (tidak wajib, jika kosong hanya Key 1 dan 2 yang digunakan)</small>
                    </div>

                    <hr class="my-4">

                    <div class="form-group mb-3">
                        <label for="change_number" class="form-label">Jumlah Pesan Sebelum Berganti Key</label>
                        <input
                            type="number"
                            class="form-control @error('change_number') is-invalid @enderror"
                            id="change_number"
                            name="change_number"
                            value="{{ old('change_number', config('services.watzap.change_number', 50)) }}"
                            placeholder="Contoh: 50"
                            min="1"
                            max="10000"
                        >
                        @error('change_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Setiap berapa pesan sistem akan berganti ke number key berikutnya (default: 50)</small>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('btn-save').addEventListener('click', function() {
        document.getElementById('form').submit();
    });
</script>
@endsection
