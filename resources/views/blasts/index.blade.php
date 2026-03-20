@extends('layouts.layout')

@section('content')
<style>
  .nav-tabs .nav-link.active {
    background-color: #f83f3a !important;
    color: #ffffff !important;
    border-color: transparent transparent #f83f3a !important;
  }

  .nav-tabs .nav-link {
    color: #7c8fac;
  }
</style>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h3 class="mb-0">Manajemen Blast Pesan WhatsApp</h3>
            <p>Manajemen blast pesan WhatsApp yang sudah dibuat.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('blasts.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> Buat Blast Pesan
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
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="blastTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="history-tab" data-bs-toggle="tab" data-bs-target="#history"
                            type="button" role="tab" aria-controls="history" aria-selected="true">
                        <i class="ti ti-history"></i> Riwayat Blast
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule"
                            type="button" role="tab" aria-controls="schedule" aria-selected="false">
                        <i class="ti ti-calendar-event"></i> Jadwal Pesan
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="blastTabContent">
                <!-- History Tab -->
                <div class="tab-pane fade show active" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">#</th>
                                    <th width="20%">Campaign</th>
                                    <th width="15%">Tipe Pesan</th>
                                    <th width="25%">Isi Pesan</th>
                                    <th width="10%">Status</th>
                                    <th width="12%">Progress</th>
                                    <th width="10%">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blastHistories as $key => $history)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <strong>{{ $history->campaign->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $history->campaign->brand->name }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                @switch($history->message_type)
                                                    @case('join_reminder')
                                                        Join Reminder
                                                    @break
                                                    @case('draft_reminder')
                                                        Draft Reminder
                                                    @break
                                                    @case('submit_reminder')
                                                        Submit Reminder
                                                    @break
                                                    @case('accepted_reminder')
                                                        Accepted Reminder
                                                    @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>{{ \Str::limit($history->message_content, 50) }}</td>
                                        <td>
                                            @if($history->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($history->status === 'sending')
                                                <span class="badge bg-info">Mengirim</span>
                                            @elseif($history->status === 'completed')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-danger">Gagal</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                @php
                                                    $total = $history->total_affiliate;
                                                    $progress = $total > 0 ? round(($history->success_count / $total) * 100, 0) : 0;
                                                @endphp
                                                <div class="progress-bar bg-success" role="progressbar"
                                                     style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $history->success_count }}/{{ $total }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $history->created_at->format('d/m/y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-muted mb-0">Belum ada riwayat blast pesan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $blastHistories->links() }}
                </div>

                <!-- Schedule Tab -->
                <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                    <div class="table-responsive mt-4">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="8%">#</th>
                                    <th width="20%">Campaign</th>
                                    <th width="15%">Jenis Pesan</th>
                                    <th width="14%">Jadwal Kirim</th>
                                    <th width="22%">Isi Pesan</th>
                                    <th width="10%">Status</th>
                                    <th width="11%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blastSchedules as $key => $schedule)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <strong>{{ $schedule->campaign->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $schedule->campaign->brand->name }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                @switch($schedule->message_type)
                                                    @case('join_reminder')
                                                        Join Reminder
                                                    @break
                                                    @case('draft_reminder')
                                                        Draft Reminder
                                                    @break
                                                    @case('submit_reminder')
                                                        Submit Reminder
                                                    @break
                                                    @case('accepted_reminder')
                                                        Accepted Reminder
                                                    @break
                                                @endswitch
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ ucfirst($schedule->frequency) }}</strong>
                                            @if($schedule->schedule_time)
                                                <br><small>{{ $schedule->schedule_time }}</small>
                                            @endif
                                        </td>
                                        <td>{{ \Str::limit($schedule->message_content, 50) }}</td>
                                        <td>
                                            @if($schedule->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning" title="Edit">
                                                <i class="ti ti-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-muted mb-0">Belum ada jadwal blast pesan</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $blastSchedules->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
