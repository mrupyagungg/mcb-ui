@extends('adminlte::page')

@section('title', 'DASHBOARD - ' . config('app.name'))

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark font-weight-bold">
                Dashboard System
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">

    <!-- 1. Row Info Boxes / Stat Cards -->
    <div class="row">
        @php
            // Menghitung total seluruh history log generate ATP atau aktivitas terkait
            $totalGenerated = \App\Models\HistoryLog::where('module', 'like', '%ATP%')->count();

            // Menghitung khusus modul ATP Distribusi
            $totalDistribusi = \App\Models\HistoryLog::where('module', 'LIKE', '%Distribusi%')->count();

            // Menghitung khusus modul ATP Subfeeder
            $totalSubfeeder = \App\Models\HistoryLog::where('module', 'LIKE', '%Subfeeder%')->count();
        @endphp

        <!-- Card Total Document -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0">
                <span class="info-box-icon bg-info elevation-1 rounded-circle"><i class="fas fa-file-excel"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Total ATP Generated</span>
                    <span
                        class="info-box-number text-dark h4 mb-0 font-weight-bold">{{ number_format($totalGenerated) }}</span>
                </div>
            </div>
        </div>

        <!-- Card ATP Distribusi -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0">
                <span class="info-box-icon bg-success elevation-1 rounded-circle"><i
                        class="fas fa-network-wired"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">ATP Distribusi (DS)</span>
                    <span
                        class="info-box-number text-dark h4 mb-0 font-weight-bold">{{ number_format($totalDistribusi) }}</span>
                </div>
            </div>
        </div>

        <!-- Card ATP Subfeeder -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0">
                <span class="info-box-icon bg-primary elevation-1 rounded-circle"><i
                        class="fas fa-project-diagram"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">ATP Subfeeder (SF)</span>
                    <span
                        class="info-box-number text-dark h4 mb-0 font-weight-bold">{{ number_format($totalSubfeeder) }}</span>
                </div>
            </div>
        </div>

        <!-- Card Quick Action Link -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0 bg-success text-white">
                <div class="info-box-content">
                    <span class="info-box-text">Aksi Cepat</span>
                    <a href="{{ route('admin.cwatp') }}"
                        class="btn btn-light btn-sm font-weight-bold mt-1 text-success shadow-xs">
                        <i class="fas fa-plus-circle mr-1"></i> Form Bulk ATP
                    </a>
                </div>
                <span class="info-box-icon"><i class="fas fa-cloud-upload-alt"></i></span>
            </div>
        </div>
    </div>

    <!-- 2. Row Charts & Quick Actions -->
    <div class="row">

        <!-- Quick Info Panel -->
        <div class="col-lg-4">
            <div class="card card-outline card-info shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark m-0">
                        <i class="fas fa-info-circle text-info mr-2"></i>Panduan Sistem Generate CW ATP
                    </h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-success mb-3 bg-light">
                        <h6 class="font-weight-bold text-success"><i class="fas fa-folder mr-1"></i> Format ZIP
                            Distribusi</h6>
                        <small class="text-secondary d-block">
                            Pastikan ZIP memiliki folder <code>Impelemntasi</code>, <code>FDT</code>, dan folder FAT
                            seperti <code>FAT_01</code>, <code>FAT_02</code>.
                        </small>
                    </div>

                    <div class="callout callout-info mb-3 bg-light">
                        <h6 class="font-weight-bold text-info"><i class="fas fa-folder mr-1"></i> Format ZIP Subfeeder
                        </h6>
                        <small class="text-secondary d-block">
                            Pastikan ZIP memiliki folder <code>IMPLEMENTASI DS</code> dan <code>FDT</code>.
                        </small>
                    </div>

                    <div class="callout callout-warning mb-0 bg-light">
                        <h6 class="font-weight-bold text-warning"><i class="fas fa-exclamation-triangle mr-1"></i> Batas
                            Maksimum</h6>
                        <small class="text-secondary d-block">
                            Maksimal <strong>20 folder FAT/Section</strong> per sekali proses ekspor file Excel.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar User Terdaftar (Baru Ditambahkan) -->
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-dark m-0">
                            Daftar User Terdaftar
                        </h3>
                        <a href="#" class="btn btn-sm btn-outline-primary">Kelola User</a>
                    </div>
                </div>

                <!-- Card Body tanpa padding agar scroll rapi di dalam kartu -->
                <div class="card-body p-0">
                    <!-- Pembungkus untuk mengaktifkan scroll vertikal & horizontal -->
                    <div style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                        <table class="table table-hover table-striped mb-0 text-nowrap">
                            <!-- Header tetap menempel di atas saat di-scroll -->
                            <thead
                                style="position: sticky; top: 0; background-color: #ffffff; z-index: 10; box-shadow: inset 0 -1px 0 #dee2e6;">
                                <tr>
                                    <th class="py-3">Nama</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Region</th>
                                    <th class="py-3">Role</th>
                                    <th class="py-3">Status / Bergabung</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <!-- Nama -->
                                        <td class="font-weight-bold align-middle">
                                            {{ $user->name }}
                                        </td>

                                        <!-- Email -->
                                        <td class="align-middle text-muted">{{ $user->email }}</td>

                                        <!-- Region -->
                                        <td class="align-middle">
                                            <span class="badge badge-info px-2 py-1">{{ $user->region ?? 'N/A' }}</span>
                                        </td>

                                        <!-- Role -->
                                        <td class="align-middle">
                                            <span class="badge badge-primary px-2 py-1">{{ $user->role ?? 'User' }}</span>
                                        </td>

                                        <!-- Status Online / Offline (Last Seen) -->
                                        <td class="align-middle">
                                            @if($user->isOnline())
                                                {{-- Status jika sedang aktif / online --}}
                                                <span class="badge px-2.5 py-1 rounded-pill"
                                                    style="background-color: rgba(16, 185, 129, 0.1); color: #059669; font-weight: 500;">
                                                    <i class="fas fa-circle text-[8px] mr-1 text-emerald-500 animate-pulse"></i>
                                                    Online
                                                </span>
                                            @else
                                                @php
                                                    // Mengecek apakah user memiliki data last_seen_at dan selisihnya <= 25 menit
                                                    $diffInMinutes = $user->last_seen_at ? $user->last_seen_at->diffInMinutes(now()) : null;
                                                @endphp

                                                @if($diffInMinutes !== null && $diffInMinutes <= 25)
                                                    {{-- Status kelipatan menit dari 1 hingga 25 menit yang lalu --}}
                                                    <span class="badge px-2.5 py-1 rounded-pill"
                                                        style="background-color: rgba(245, 158, 11, 0.1); color: #d97706; font-weight: 500;">
                                                        <i class="fas fa-clock text-[8px] mr-1 text-amber-500"></i>
                                                        {{ $user->last_seen_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    {{-- Status jika sudah lebih dari 25 menit atau belum pernah login --}}
                                                    <span class="badge px-2.5 py-1 rounded-pill"
                                                        style="background-color: rgba(148, 163, 184, 0.1); color: #64748b; font-weight: 500;">
                                                        <i class="fas fa-circle text-[8px] mr-1 text-slate-400"></i>
                                                        Offline
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Tidak ada user yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Row Riwayat Aktivitas Ekspor Terakhir -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-secondary shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-dark m-0">
                            <i class="fas fa-history text-secondary mr-2"></i>Riwayat Aktivitas Sistem Terakhir
                        </h3>
                        <a href="#" class="btn btn-sm btn-outline-primary disabled">Lihat Semua Log</a>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped mb-0 text-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th>Time</th>
                                <th>User</th>
                                <th>Modul / Fitur</th>
                                <th>Action</th>
                                <th>Region</th>
                                <th>Cluster Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $historyLogs = \App\Models\HistoryLog::with('user')->latest()->take(6)->get();
                            @endphp

                            @forelse($historyLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        <span class="font-weight-bold text-indigo">
                                            <i class="fas fa-user-circle mr-1 text-capitalize"></i>
                                            {{ $log->user->name ?? 'User' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge                                                                              @if (str_contains($log->module, 'ATP')) badge-success 
                                        @elseif(str_contains($log->module, 'OPM')) badge-info 
                                            @else badge-warning 
                                                @endif">
                                            {{ $log->module }}
                                        </span>
                                    </td>
                                    <td>{{ $log->action_type }}</td>
                                    <td>{{ $log->region ?? '-' }}</td>
                                    <td>
                                        <span class="font-weight-semibold">{{ $log->description }}</span>
                                        @if ($log->description)
                                            <br><small class="text-muted">{{ $log->target_name }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-subtle badge-success text-success font-weight-bold">
                                            <i class="fas fa-check-circle mr-1"></i> {{ $log->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat aktivitas
                                        sistem.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('css')
<style>
    .info-box {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .info-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .badge-subtle {
        background-color: rgba(40, 167, 69, 0.1);
        padding: 5px 10px;
        border-radius: 4px;
    }
</style>
@stop

@section('js')
<!-- Perbaikan sintaks selector jQuery untuk Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {
        // Jika Anda ingin mengaktifkan kembali chart-nya, pastikan elemen <canvas id="atpExportChart"></canvas> sudah ada di HTML.

        // var ctx = document.getElementById('atpExportChart').getContext('2d');
        // var atpChart = new Chart(ctx, {
        //     type: 'bar',
        //     data: {
        //         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep'],
        //         datasets: [
        //             {
        //                 label: 'ATP Distribusi',
        //                 backgroundColor: '#28a745',
        //                 borderColor: '#28a745',
        //                 data: [65, 59, 80, 81, 56, 55, 90, 110, 120]
        //             },
        //             {
        //                 label: 'ATP Subfeeder',
        //                 backgroundColor: '#17a2b8',
        //                 borderColor: '#17a2b8',
        //                 data: [28, 48, 40, 19, 86, 27, 60, 75, 88]
        //             }
        //         ]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         scales: {
        //             y: {
        //                 beginAtZero: true
        //             }
        //         }
        //     }
        // });

    });
</script>
@stop