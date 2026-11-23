@extends('adminlte::page')

@section('title', 'Dashboard HRD')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="font-weight-bold mb-0">Dashboard HRD</h1>
        <small class="text-muted">Human Resource Development - MCB</small>
    </div>
    <span class="badge badge-success p-2">
        <i class="fas fa-circle mr-1"></i> HRD Panel
    </span>
</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- WELCOME --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="font-weight-bold">
                        Selamat Datang, {{ auth()->user()->name }}!
                    </h4>
                    <p class="text-muted mb-0">
                        Berikut merupakan ringkasan aktivitas kehadiran
                        karyawan MCB hari ini.
                    </p>
                </div>
                <div class="col-md-4 text-md-right mt-3 mt-md-0">
                    <h5 class="font-weight-bold mb-1">
                        <i class="far fa-calendar-alt text-primary mr-2"></i>
                        {{ now()->translatedFormat('l, d F Y') }}
                    </h5>
                    <small class="text-muted">
                        <i class="far fa-clock mr-1"></i>
                        {{ now()->format('H:i') }} WIB
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- STATISTIK --}}
    <div class="row">

        {{-- TOTAL KARYAWAN --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalKaryawan) }}</h3>
                    <p>Total Karyawan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('hrd.karyawan') }}" class="small-box-footer">
                    Lihat Karyawan <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- HADIR --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalHadir) }}</h3>
                    <p>Hadir Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <a href="{{ route('hrd.riwayat-absensi') }}" class="small-box-footer">
                    Lihat Absensi <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- TERLAMBAT --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($totalTerlambat) }}</h3>
                    <p>Terlambat Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <a href="{{ route('hrd.riwayat-absensi') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        {{-- BELUM ABSEN --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ number_format($belumAbsen) }}</h3>
                    <p>Belum Absen</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-times"></i>
                </div>
                <a href="{{ route('hrd.riwayat-absensi') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

    </div>

    {{-- TABEL ABSENSI --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-history mr-2 text-primary"></i>
                Aktivitas Absensi Hari Ini
            </h3>

            <div class="card-tools">
                <a href="{{ route('hrd.riwayat-absensi') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list mr-1"></i>
                    Semua Riwayat
                </a>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">

                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Karyawan</th>
                        <th>Email</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($absensiTerbaru as $index => $absensi)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <div class="font-weight-bold">
                                    {{ $absensi->name }}
                                </div>
                            </td>

                            <td>{{ $absensi->email }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $absensi->jam_masuk ?? '-' }}
                            </td>

                            <td>
                                {{ $absensi->jam_keluar ?? '-' }}
                            </td>

                            <td>
                                @if($absensi->status_masuk === 'terlambat')
                                    <span class="badge badge-warning">
                                        Terlambat
                                    </span>
                                @elseif($absensi->status_masuk === 'tepat_waktu')
                                    <span class="badge badge-success">
                                        Tepat Waktu
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        {{ $absensi->status_masuk ?? 'Belum Ada Status' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">
                                    Belum ada aktivitas absensi hari ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="card-footer text-muted">
            <small>
                <i class="fas fa-info-circle mr-1"></i>
                Menampilkan maksimal 10 aktivitas absensi terbaru.
            </small>
        </div>

    </div>

</div>

@stop

@section('css')
<style>
    .small-box {
        border-radius: 8px;
        overflow: hidden;
    }

    .small-box .inner h3 {
        font-weight: 700;
    }

    .card {
        border-radius: 8px;
    }

    .table td,
    .table th {
        vertical-align: middle;
        white-space: nowrap;
    }

    .table thead th {
        border-top: none;
    }
</style>
@stop