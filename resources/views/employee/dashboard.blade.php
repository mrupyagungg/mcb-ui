@extends('adminlte::page')

@section('title', 'Dashboard Absensi - ' . config('app.name'))

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark font-weight-bold">
                Dashboard Absensi
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                    <a href="#">Home</a>
                </li>
                <li class="breadcrumb-item active">
                    Dashboard
                </li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- Welcome --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1">
                        Selamat datang,
                        <strong>{{ auth()->user()->name }}</strong> 👋
                    </h4>
                    <p class="text-muted mb-0">
                        Silakan lakukan absensi sesuai jadwal kerja Anda.
                    </p>
                </div>

                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <a href="{{ route('employee.attendance') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Buka Absensi
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row">

        {{-- Jam Masuk --}}
        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3 id="dashboardJamMasuk">
                        {{ isset($absensi) && $absensi?->jam_masuk ? $absensi->jam_masuk : '--:--' }}
                    </h3>
                    <p>Jam Masuk Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-in-alt"></i>
                </div>
            </div>
        </div>

        {{-- Jam Keluar --}}
        <div class="col-lg-4 col-md-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3 id="dashboardJamKeluar">
                        {{ isset($absensi) && $absensi?->jam_keluar ? $absensi->jam_keluar : '--:--' }}
                    </h3>
                    <p>Jam Keluar Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="col-lg-4 col-md-12">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">

                    @if(isset($absensi) && $absensi?->jam_keluar)
                        <h3>Selesai</h3>
                        <p>Absensi Hari Ini</p>

                    @elseif(isset($absensi) && $absensi?->jam_masuk)
                        <h3>Aktif</h3>
                        <p>Sedang Bekerja</p>

                    @else
                        <h3>Belum</h3>
                        <p>Belum Absen Masuk</p>
                    @endif

                </div>

                <div class="icon">
                    <i class="fas fa-user-clock"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Skor Absensi Bulanan --}}
    <div class="card card-outline {{ $scoreColor }} shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-star mr-2"></i>
                Skor Absensi Bulanan
            </h3>

            <div class="card-tools">
                <span class="badge badge-light">
                    {{ now()->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <div class="card-body">

            <div class="row align-items-center">

                {{-- Skor --}}
                <div class="col-md-4 text-center border-right">

                    <small class="text-muted d-block">
                        TOTAL SKOR
                    </small>

                    <div class="mt-2">
                        <span class="display-3 font-weight-bold text-{{ $scoreColor }}">
                            {{ number_format($attendanceScore, 0) }}
                        </span>

                        <span class="text-muted font-weight-bold">
                            / 100
                        </span>
                    </div>

                    <div class="mt-2">
                        @if($attendanceScore >= 70)

                            <span class="badge badge-success p-2">
                                <i class="fas fa-check-circle mr-1"></i>
                                Baik
                            </span>

                        @elseif($attendanceScore >= 50)

                            <span class="badge badge-warning p-2">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Perlu Perhatian
                            </span>

                        @else

                            <span class="badge badge-danger p-2">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Rendah
                            </span>

                        @endif
                    </div>

                </div>

                {{-- Kehadiran --}}
                <div class="col-md-4 text-center border-right">

                    <small class="text-muted d-block">
                        KEHADIRAN
                    </small>

                    <h2 class="font-weight-bold mt-3 mb-1">
                        {{ $attendancePresent }}
                        <small class="text-muted">
                            / {{ $attendanceWorkdays }}
                        </small>
                    </h2>

                    <p class="text-muted mb-0">
                        Hari kerja
                    </p>

                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-{{ $scoreColor }}" role="progressbar"
                            style="width: {{ $attendanceScore }}%;">
                        </div>
                    </div>

                </div>

                {{-- Tidak Absen --}}
                <div class="col-md-4 text-center">

                    <small class="text-muted d-block">
                        TIDAK HADIR / TIDAK ABSEN
                    </small>

                    <h2 class="text-danger font-weight-bold mt-3 mb-1">
                        {{ $attendanceAbsent }}
                    </h2>

                    <p class="text-muted mb-0">
                        Hari kerja
                    </p>

                    @if($attendanceAbsent > 0)

                        <small class="text-danger d-block mt-2">
                            -{{ number_format($attendanceDeduction, 2) }}
                            poin per hari
                        </small>

                    @else

                        <small class="text-success d-block mt-2">
                            <i class="fas fa-check-circle"></i>
                            Tidak ada pengurangan
                        </small>

                    @endif

                </div>

            </div>

        </div>

        <div class="card-footer">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>

                        Skor dihitung berdasarkan kehadiran pada hari kerja
                        bulan {{ now()->translatedFormat('F Y') }}.
                    </small>

                </div>

                <div class="col-md-4 text-md-right mt-2 mt-md-0">

                    <a href="{{ route('employee.history') }}" class="btn btn-outline-secondary btn-sm">

                        <i class="fas fa-history mr-1"></i>
                        Lihat Riwayat

                    </a>

                </div>

            </div>

        </div>

    </div>

    {{-- Informasi --}}
    <div class="row">

        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle mr-2"></i>
                        Informasi Absensi
                    </h3>
                </div>

                <div class="card-body">

                    <ul class="list-group list-group-flush">

                        <li class="list-group-item px-0">
                            <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                            Absensi menggunakan lokasi GPS.
                        </li>

                        <li class="list-group-item px-0">
                            <i class="fas fa-clock text-primary mr-2"></i>
                            Pastikan GPS perangkat aktif.
                        </li>

                        <li class="list-group-item px-0">
                            <i class="fas fa-mobile-alt text-success mr-2"></i>
                            Gunakan perangkat yang memiliki GPS.
                        </li>

                        <li class="list-group-item px-0">
                            <i class="fas fa-camera text-warning mr-2"></i>
                            Ikuti proses verifikasi apabila diminta.
                        </li>

                    </ul>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card shadow-sm">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user mr-2"></i>
                        Profil Employee
                    </h3>
                </div>

                <div class="card-body">

                    <div class="d-flex align-items-center">

                        <div class="mr-3">
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                style="width:60px;height:60px;">

                                <i class="fas fa-user text-white fa-lg"></i>

                            </div>
                        </div>

                        <div>

                            <h5 class="mb-1 font-weight-bold">
                                {{ auth()->user()->name }}
                            </h5>

                            <p class="mb-1 text-muted">
                                <i class="fas fa-envelope mr-1"></i>
                                {{ auth()->user()->email }}
                            </p>

                            <span class="badge badge-primary">
                                Employee
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



</div>

@stop