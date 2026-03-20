@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Pengaturan Pesan</h3>
            <p>Atur pesan default masing-masing tipe.</p>
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
                <form action="{{ route('settings.message.update') }}" id="form" method="POST">
                    @csrf
                    @foreach ($messages as $message)
                        <input type="hidden" value="{{ $message->type }}" name="types[]">
                        <div class="form-group mb-3">
                            <label for="">{{ ucwords(str_replace('_', ' ', $message->type)) }}</label>
                            <textarea name="message[]" class="form-control" id="" cols="5" rows="5">{{ $message->message }}</textarea>
                        </div>
                    @endforeach
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.getElementById('btn-save').addEventListener('click', function() {
        document.getElementById('form').submit();
    });
</script>
@endpush
