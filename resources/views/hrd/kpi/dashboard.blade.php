@extends('adminlte::page')

@section('title', 'Dashboard KPI HRD')

@section('content_header')
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div>
        <h1 class="font-weight-bold mb-1">
            <i class="fas fa-chart-line text-primary mr-2"></i>
            Dashboard KPI
        </h1>
        <p class="text-muted mb-0">
            Monitoring kinerja karyawan dan koordinator seluruh area MCB
        </p>
    </div>
    <div class="mt-2">
        <span class="badge badge-primary p-2">
            <i class="far fa-calendar-alt mr-1"></i>
            Periode November 2026
        </span>
    </div>
</div>
@stop

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | DATA CONTOH
    |--------------------------------------------------------------------------
    | Nantinya data ini diambil dari database melalui KPIController.
    */

    $totalKaryawan = $totalKaryawan ?? 124;
    $sudahDinilai = $sudahDinilai ?? 98;
    $belumDinilai = $belumDinilai ?? 26;
    $totalKoordinator = $totalKoordinator ?? 12;
    $laporanMasuk = $laporanMasuk ?? 9;
    $laporanBelumMasuk = $laporanBelumMasuk ?? 2;
    $laporanRevisi = $laporanRevisi ?? 1;
    $rataRataKpi = $rataRataKpi ?? 88.3;

    $persentasePenilaian = $totalKaryawan > 0
        ? round(($sudahDinilai / $totalKaryawan) * 100)
        : 0;

    $monitoringKoordinator = $monitoringKoordinator ?? [
        [
            'nama' => 'Andi Pratama',
            'area' => 'Area A',
            'tim' => 'Lapangan',
            'laporan' => 'Lengkap',
            'nilai' => 90,
            'terakhir' => '18 Nov 2026',
        ],
        [
            'nama' => 'Budi Santoso',
            'area' => 'Area B',
            'tim' => 'Dokumen',
            'laporan' => 'Terlambat',
            'nilai' => 78,
            'terakhir' => '16 Nov 2026',
        ],
        [
            'nama' => 'Dedi Kurniawan',
            'area' => 'Area C',
            'tim' => 'Drafter',
            'laporan' => 'Lengkap',
            'nilai' => 87,
            'terakhir' => '18 Nov 2026',
        ],
        [
            'nama' => 'Rian Saputra',
            'area' => 'Area D',
            'tim' => 'Lapangan',
            'laporan' => 'Revisi',
            'nilai' => 72,
            'terakhir' => '17 Nov 2026',
        ],
    ];

    $aktivitas = $aktivitas ?? [
        [
            'judul' => 'Andi mengirim laporan mingguan',
            'keterangan' => 'Area A - Lapangan',
            'waktu' => '18 November 2026, 09:30',
            'icon' => 'fa-file-upload',
            'warna' => 'success',
        ],
        [
            'judul' => 'HRD meminta revisi laporan',
            'keterangan' => 'Area D - Lapangan',
            'waktu' => '18 November 2026, 08:15',
            'icon' => 'fa-exclamation-circle',
            'warna' => 'warning',
        ],
        [
            'judul' => 'Penilaian karyawan diselesaikan',
            'keterangan' => 'Area C - Drafter',
            'waktu' => '17 November 2026, 16:20',
            'icon' => 'fa-check-circle',
            'warna' => 'primary',
        ],
    ];
@endphp

{{-- INFO DATA CONTOH --}}
<div class="alert alert-info alert-dismissible">
    <i class="fas fa-info-circle mr-1"></i>
    Dashboard saat ini menggunakan data contoh. Hubungkan dengan database
    melalui KPIController untuk menampilkan data aktual.
</div>

{{-- =========================================================
RINGKASAN KPI
========================================================= --}}
<div class="row">

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info shadow-sm">
            <div class="inner">
                <h3>{{ $totalKaryawan }}</h3>
                <p>Total Karyawan</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="{{ route('hrd.karyawan') }}" class="small-box-footer">
                Data Karyawan <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-success shadow-sm">
            <div class="inner">
                <h3>{{ $sudahDinilai }}</h3>
                <p>Karyawan Sudah Dinilai</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-check"></i>
            </div>
            <a href="{{ route('hrd.kpi.penilaian') }}" class="small-box-footer">
                Lihat Penilaian <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning shadow-sm">
            <div class="inner">
                <h3>{{ $belumDinilai }}</h3>
                <p>Belum Dinilai</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-clock"></i>
            </div>
            <a href="{{ route('hrd.kpi.penilaian') }}" class="small-box-footer">
                Periksa Penilaian <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary shadow-sm">
            <div class="inner">
                <h3>{{ number_format($rataRataKpi, 1) }}</h3>
                <p>Rata-rata Nilai KPI</p>
            </div>
            <div class="icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <a href="{{ route('hrd.kpi.rekapitulasi') }}" class="small-box-footer">
                Rekapitulasi KPI <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

</div>

{{-- =========================================================
PROGRESS DAN MONITORING LAPORAN
========================================================= --}}
<div class="row">

    <div class="col-lg-8">
        <div class="card card-outline card-primary shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-tasks mr-2 text-primary"></i>
                    Progress Penilaian Karyawan
                </h3>
            </div>

            <div class="card-body">

                <div class="d-flex justify-content-between mb-2">
                    <span class="font-weight-bold">Penyelesaian Penilaian</span>
                    <span class="font-weight-bold text-primary">
                        {{ $persentasePenilaian }}%
                    </span>
                </div>

                <div class="progress progress-sm mb-3">
                    <div class="progress-bar bg-primary" style="width: {{ $persentasePenilaian }}%">
                    </div>
                </div>

                <div class="row text-center mt-4">

                    <div class="col-4 border-right">
                        <h3 class="text-success font-weight-bold">
                            {{ $sudahDinilai }}
                        </h3>
                        <span class="text-muted">Sudah Dinilai</span>
                    </div>

                    <div class="col-4 border-right">
                        <h3 class="text-warning font-weight-bold">
                            {{ $belumDinilai }}
                        </h3>
                        <span class="text-muted">Belum Dinilai</span>
                    </div>

                    <div class="col-4">
                        <h3 class="text-primary font-weight-bold">
                            {{ $totalKaryawan }}
                        </h3>
                        <span class="text-muted">Total Karyawan</span>
                    </div>

                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <i class="fas fa-check-circle text-success mr-1"></i>
                        Penilaian yang sudah diselesaikan
                    </div>
                    <div class="col-md-6 mb-2">
                        <i class="fas fa-clock text-warning mr-1"></i>
                        Penilaian yang masih menunggu
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-warning shadow-sm h-100">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-clipboard-check mr-2 text-warning"></i>
                    Status Laporan Koordinator
                </h3>
            </div>

            <div class="card-body">

                <div class="text-center mb-3">
                    <h2 class="font-weight-bold text-primary">
                        {{ $totalKoordinator }}
                    </h2>
                    <span class="text-muted">Total Koordinator</span>
                </div>

                <ul class="list-group list-group-unbordered">

                    <li class="list-group-item">
                        Laporan Lengkap
                        <span class="float-right badge badge-success">
                            {{ $laporanMasuk }}
                        </span>
                    </li>

                    <li class="list-group-item">
                        Belum Melapor
                        <span class="float-right badge badge-warning">
                            {{ $laporanBelumMasuk }}
                        </span>
                    </li>

                    <li class="list-group-item">
                        Perlu Revisi
                        <span class="float-right badge badge-danger">
                            {{ $laporanRevisi }}
                        </span>
                    </li>

                </ul>

                <a href="{{ route('hrd.kpi.penilaian') }}" class="btn btn-outline-primary btn-block mt-3">
                    <i class="fas fa-search mr-1"></i>
                    Periksa Laporan
                </a>

            </div>
        </div>
    </div>

</div>

{{-- =========================================================
GRAFIK KINERJA TIM
========================================================= --}}
<div class="row">

    <div class="col-lg-8">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-chart-column mr-2 text-primary"></i>
                    Rata-rata KPI Berdasarkan Tim
                </h3>
            </div>

            <div class="card-body">
                <div style="height: 300px;">
                    <canvas id="chartKpiTim"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-chart-pie mr-2 text-success"></i>
                    Status Penilaian
                </h3>
            </div>

            <div class="card-body">
                <div style="height: 250px;">
                    <canvas id="chartStatusKpi"></canvas>
                </div>

                <div class="text-center mt-3">
                    <span class="badge badge-success mr-2">
                        Sudah dinilai
                    </span>
                    <span class="badge badge-warning">
                        Belum dinilai
                    </span>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- =========================================================
MONITORING KOORDINATOR
========================================================= --}}
<div class="card card-outline card-primary shadow-sm">

    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-user-tie mr-2 text-primary"></i>
            Monitoring Kinerja Koordinator
        </h3>

        <div class="card-tools">
            <span class="badge badge-info">
                {{ count($monitoringKoordinator) }} Koordinator
            </span>
        </div>
    </div>

    <div class="card-body table-responsive p-0">

        <table class="table table-hover table-striped text-nowrap mb-0">

            <thead class="bg-light">
                <tr>
                    <th width="50">No.</th>
                    <th>Nama Koordinator</th>
                    <th>Area</th>
                    <th>Tim</th>
                    <th>Nilai KPI</th>
                    <th>Status Laporan</th>
                    <th>Terakhir Melapor</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($monitoringKoordinator as $index => $koordinator)

                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td class="font-weight-bold">
                            {{ $koordinator['nama'] }}
                        </td>

                        <td>{{ $koordinator['area'] }}</td>

                        <td>{{ $koordinator['tim'] }}</td>

                        <td>
                            @php
                                $nilai = $koordinator['nilai'];

                                $warnaNilai = $nilai >= 85
                                    ? 'success'
                                    : ($nilai >= 70 ? 'warning' : 'danger');
                            @endphp

                            <span class="badge badge-{{ $warnaNilai }} p-2">
                                {{ $nilai }}
                            </span>
                        </td>

                        <td>
                            @if($koordinator['laporan'] == 'Lengkap')
                                <span class="badge badge-success">
                                    <i class="fas fa-check mr-1"></i>
                                    Lengkap
                                </span>
                            @elseif($koordinator['laporan'] == 'Terlambat')
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock mr-1"></i>
                                    Terlambat
                                </span>
                            @else
                                <span class="badge badge-danger">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Revisi
                                </span>
                            @endif
                        </td>

                        <td>{{ $koordinator['terakhir'] }}</td>

                        <td>
                            <a href="{{ route('hrd.kpi.penilaian') }}" class="btn btn-xs btn-primary">
                                <i class="fas fa-eye mr-1"></i>
                                Detail
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center text-muted p-4">
                            Belum ada data koordinator.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="card-footer text-right">
        <a href="{{ route('hrd.kpi.rekapitulasi') }}" class="btn btn-sm btn-outline-primary">
            Lihat Rekapitulasi
            <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>

</div>

{{-- =========================================================
AKTIVITAS TERBARU
========================================================= --}}
<div class="card card-outline card-secondary shadow-sm">

    <div class="card-header">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-history mr-2 text-secondary"></i>
            Aktivitas KPI Terbaru
        </h3>
    </div>

    <div class="card-body">

        @forelse($aktivitas as $item)

            <div class="d-flex align-items-start mb-3">

                <div class="mr-3">
                    <span class="bg-{{ $item['warna'] }} rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 38px; height: 38px;">
                        <i class="fas {{ $item['icon'] }} text-white"></i>
                    </span>
                </div>

                <div class="flex-grow-1">
                    <div class="font-weight-bold">
                        {{ $item['judul'] }}
                    </div>

                    <div class="text-muted small">
                        {{ $item['keterangan'] }}
                    </div>

                    <div class="text-muted small">
                        <i class="far fa-clock mr-1"></i>
                        {{ $item['waktu'] }}
                    </div>
                </div>

            </div>

        @empty

            <p class="text-center text-muted mb-0">
                Belum ada aktivitas KPI.
            </p>

        @endforelse

    </div>

</div>

@stop

{{-- =========================================================
CSS
========================================================= --}}
@section('css')
<style>
    .small-box {
        border-radius: 8px;
        overflow: hidden;
    }

    .card {
        border-radius: 8px;
    }

    .card-header {
        padding: 12px 16px;
    }

    .table th,
    .table td {
        vertical-align: middle !important;
    }

    .table thead th {
        border-bottom: 1px solid #dee2e6;
        font-size: 13px;
    }

    .table tbody td {
        font-size: 13px;
    }

    .progress {
        border-radius: 10px;
        height: 10px;
    }

    .list-group-item {
        padding-left: 0;
        padding-right: 0;
    }

    @media (max-width: 767px) {
        .content-header h1 {
            font-size: 21px;
        }
    }
</style>
@stop

{{-- =========================================================
CHART JS
========================================================= --}}
@section('plugins.Chartjs', true)

@section('js')
<script>
    $(function () {

        // Grafik rata-rata KPI setiap tim
        const chartTim = document.getElementById('chartKpiTim');

        if (chartTim) {
            new Chart(chartTim, {
                type: 'bar',
                data: {
                    labels: ['Lapangan', 'Dokumen', 'Drafter'],
                    datasets: [{
                        label: 'Rata-rata KPI',
                        data: [86, 91, 88],
                        backgroundColor: [
                            'rgba(60, 141, 188, 0.85)',
                            'rgba(40, 167, 69, 0.85)',
                            'rgba(111, 66, 193, 0.85)'
                        ],
                        borderRadius: 6,
                        maxBarThickness: 65
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                stepSize: 20
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return 'Nilai KPI: ' + context.raw;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Grafik status penilaian
        const chartStatus = document.getElementById('chartStatusKpi');

        if (chartStatus) {
            new Chart(chartStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Sudah Dinilai', 'Belum Dinilai'],
                    datasets: [{
                        data: [
                            {{ $sudahDinilai }},
                            {{ $belumDinilai }}
                        ],
                        backgroundColor: [
                            '#28a745',
                            '#ffc107'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

    });
</script>
@stop