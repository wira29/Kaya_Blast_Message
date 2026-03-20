@extends('layouts.layout')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/libs/select2/dist/css/select2.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="mb-0">Buat Blast Pesan WhatsApp</h3>
            <p>Buat blast pesan WhatsApp untuk campaign yang sudah ada.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('blasts.store') }}" method="POST" id="blastForm">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="campaign_id" class="form-label">Campaign <span class="text-danger">*</span></label>
                            <select class="form-select select2-campaign @error('campaign_id') is-invalid @enderror"
                                    id="campaign_id" name="campaign_id" required>
                                <option value="">-- Pilih Campaign --</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                        {{ $campaign->name }} ({{ $campaign->affiliates_count }} Affiliate)
                                    </option>
                                @endforeach
                            </select>
                            @error('campaign_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="message_type" class="form-label">Tipe Pesan <span class="text-danger">*</span></label>
                            <select class="form-select @error('message_type') is-invalid @enderror"
                                    id="message_type" name="message_type" required>
                                <option value="">-- Pilih Tipe Pesan --</option>
                                <option value="join_reminder" {{ old('message_type') == 'join_reminder' ? 'selected' : '' }}>
                                    Join Reminder (Ingatkan untuk bergabung)
                                </option>
                                <option value="draft_reminder" {{ old('message_type') == 'draft_reminder' ? 'selected' : '' }}>
                                    Draft Reminder (Ingatkan draft)
                                </option>
                                <option value="submit_reminder" {{ old('message_type') == 'submit_reminder' ? 'selected' : '' }}>
                                    Submit Reminder (Ingatkan submit)
                                </option>
                                <option value="accepted_reminder" {{ old('message_type') == 'accepted_reminder' ? 'selected' : '' }}>
                                    Accepted Reminder (Ingatkan diterima)
                                </option>
                            </select>
                            @error('message_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="message_content" class="form-label">Isi Pesan <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('message_content') is-invalid @enderror"
                              id="message_content" name="message_content" rows="6"
                              placeholder="Ketik pesan yang akan dikirim ke semua affiliate"
                              required>{{ old('message_content') }}</textarea>
                    <small class="text-muted">Maksimal 5000 karakter</small>
                    <small class="text-muted d-block mt-2">
                        <strong>Variabel yang tersedia:</strong><br>
                        <code>{campaign}</code> = Nama campaign yang di-blast<br>
                        <code>{user}</code> = Nama target/affiliate penerima pesan
                    </small>
                    @error('message_content')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="frequency" class="form-label">Frekuensi Pengiriman <span class="text-danger">*</span></label>
                            <select class="form-select @error('frequency') is-invalid @enderror"
                                    id="frequency" name="frequency" required>
                                <option value="once" {{ old('frequency') == 'once' ? 'selected' : '' }}>
                                    Sekali Saja (Langsung)
                                </option>
                                <option value="hourly" {{ old('frequency') == 'hourly' ? 'selected' : '' }}>
                                    Per Jam
                                </option>
                                <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>
                                    Per Hari
                                </option>
                                <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>
                                    Per Minggu
                                </option>
                                <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>
                                    Per Bulan
                                </option>
                            </select>
                            @error('frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="schedule_time" class="form-label">Waktu Pengiriman</label>
                            <input type="time" class="form-control @error('schedule_time') is-invalid @enderror"
                                   id="schedule_time" name="schedule_time"
                                   value="{{ old('schedule_time') }}">
                            <small class="text-muted">Opsional, format HH:MM</small>
                            @error('schedule_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="schedule_day" class="form-label">Hari/Tanggal Pengiriman</label>
                    <input type="text" class="form-control @error('schedule_day') is-invalid @enderror"
                           id="schedule_day" name="schedule_day" placeholder="Misal: MON, TUE, atau 1-31"
                           value="{{ old('schedule_day') }}">
                    <small class="text-muted">
                        Untuk mingguan: MON, TUE, WED, THU, FRI, SAT, SUN<br>
                        Untuk bulanan: 1-31
                    </small>
                    @error('schedule_day')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info" role="alert">
                    <h6 class="alert-heading"><i class="ti ti-info-circle"></i> Informasi Pengiriman</h6>
                    <ul class="mb-0">
                        <li>Pesan akan dikirim ke <strong>semua affiliate</strong> pada campaign yang dipilih</li>
                        <li>Setiap pesan memiliki <strong>delay 15 detik</strong> untuk menghindari pembanned</li>
                        <li>Status pengiriman dapat dipantau di tab <strong>Riwayat Blast</strong></li>
                        <li>Untuk jadwal tertentu, pesan akan dikirim secara otomatis sesuai jadwal</li>
                    </ul>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-check"></i> Kirim Blast Pesan
                    </button>
                    <a href="{{ route('blasts.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script src="{{ asset('') }}assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="{{ asset('') }}assets/libs/select2/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for Campaign
        $('.select2-campaign').select2({
            search: true
        });

        $('#message_type').change(function() {
            const messageType = $(this).val();
            const defaultMessages = {!! json_encode($defaultMessages) !!};
            const message = defaultMessages.find(message => message.type === messageType)
            $('#message_content').val(message.message);
        })

        // Show/hide schedule fields based on frequency
        $('#frequency').on('change', function() {
            const frequency = $(this).val();
            const scheduleTime = $('#schedule_time').closest('.mb-3');
            const scheduleDay = $('#schedule_day').closest('.mb-3');

            if (frequency === 'once') {
                scheduleTime.hide();
                scheduleDay.hide();
            } else {
                scheduleTime.show();
                if (frequency === 'weekly' || frequency === 'monthly') {
                    scheduleDay.show();
                } else {
                    scheduleDay.hide();
                }
            }
        });

        // Initial trigger
        $('#frequency').trigger('change');

        // Form validation
        $('#blastForm').on('submit', function(e) {
            const frequency = $('#frequency').val();
            const messageType = $('#message_type').val();
            const messageContent = $('#message_content').val();

            if (!messageType) {
                e.preventDefault();
                alert('Tipe pesan harus dipilih');
                return false;
            }

            if (!messageContent || messageContent.trim().length === 0) {
                e.preventDefault();
                alert('Isi pesan harus diisi');
                return false;
            }
        });
    });
</script>
@endpush
@endsection
