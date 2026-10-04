@extends('adminlte::page')

@section('title', 'Riwayat - ' . config('app.name'))

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark font-weight-bold">
                Riwayat Absensi
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                    <a href="{{ route('employee.dashboard') }}">Home</a>
                </li>
                <li class="breadcrumb-item active">
                    Riwayat
                </li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')

<div class="container-fluid">

    {{-- Filter --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-filter mr-2"></i>
                Filter Riwayat
            </h3>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('employee.history') }}">
                <div class="row align-items-end">

                    <div class="col-md-4">
                        <label>Bulan</label>
                        <select name="bulan" class="form-control">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ (int) ($bulan ?? now()->month) === $i ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Tahun</label>
                        <select name="tahun" class="form-control">
                            @for($tahun = now()->year; $tahun >= now()->year - 3; $tahun--)
                                <option value="{{ $tahun }}" {{ (int) ($tahunDipilih ?? now()->year) === $tahun ? 'selected' : '' }}>
                                    {{ $tahun }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-4 mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search mr-1"></i>
                            Tampilkan
                        </button>

                        <a href="{{ route('employee.history') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Reset
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row">

        {{-- Total Hari Absen --}}
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $totalAbsensi ?? 0 }}</h3>
                    <p>Total Absensi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        {{-- Tepat Waktu --}}
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $totalTepatWaktu ?? 0 }}</h3>
                    <p>Tepat Waktu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        {{-- Terlambat --}}
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $totalTerlambat ?? 0 }}</h3>
                    <p>Terlambat</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>

        {{-- Tidak Absen --}}
        <div class="col-lg-3 col-md-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $totalTidakAbsen ?? 0 }}</h3>
                    <p>Tidak Absen</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Judul Periode --}}
    <div class="card shadow-sm">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>
                Riwayat Absensi
            </h3>

            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ \Carbon\Carbon::createFromDate($tahunDipilih ?? now()->year, $bulan ?? now()->month, 1)->translatedFormat('F Y') }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">

            @if(isset($absensi) && $absensi->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover table-striped mb-0">

                        <thead class="bg-light">

                            <tr>
                                <th class="text-center" width="60">
                                    No
                                </th>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    Hari
                                </th>

                                <th>
                                    Jam Masuk
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Jam Keluar
                                </th>

                                <th>
                                    Lokasi Masuk
                                </th>

                                <th class="text-center">
                                    Detail
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($absensi as $index => $item)

                                @php
                                    $tanggal = \Carbon\Carbon::parse($item->tanggal);
                                @endphp

                                <tr>

                                    <td class="text-center">
                                        {{ $index + 1 }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $tanggal->translatedFormat('d F Y') }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $tanggal->translatedFormat('l') }}
                                    </td>

                                    <td>
                                        @if($item->jam_masuk)
                                            <span class="font-weight-bold text-primary">
                                                <i class="fas fa-sign-in-alt mr-1"></i>
                                                {{ $item->jam_masuk }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                --
                                            </span>
                                        @endif
                                    </td>

                                    <td>

                                        @if($item->status_masuk === 'tepat_waktu')

                                            <span class="badge badge-success">
                                                <i class="fas fa-check mr-1"></i>
                                                Tepat Waktu
                                            </span>

                                        @elseif($item->status_masuk === 'terlambat')

                                            <span class="badge badge-warning">
                                                <i class="fas fa-clock mr-1"></i>
                                                Terlambat
                                            </span>

                                        @else

                                            <span class="badge badge-secondary">
                                                {{ ucfirst($item->status_masuk ?? 'Tidak Ada') }}
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($item->jam_keluar)

                                            <span class="font-weight-bold text-success">
                                                <i class="fas fa-sign-out-alt mr-1"></i>
                                                {{ $item->jam_keluar }}
                                            </span>

                                        @else

                                            <span class="text-muted">
                                                --
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @if($item->kota_masuk || $item->jalan_masuk)

                                            <small>
                                                <i class="fas fa-map-marker-alt text-danger mr-1"></i>

                                                {{ $item->jalan_masuk ?? '-' }}

                                                @if($item->kota_masuk)
                                                    <br>
                                                    <span class="text-muted ml-3">
                                                        {{ $item->kota_masuk }}
                                                    </span>
                                                @endif
                                            </small>

                                        @else

                                            <span class="text-muted">
                                                -
                                            </span>

                                        @endif

                                    </td>

                                    <td class="text-center">

                                        <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                            data-target="#detailModal{{ $item->id }}">

                                            <i class="fas fa-eye"></i>

                                        </button>

                                    </td>

                                </tr>

                                {{-- Modal Detail --}}
                                <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" role="dialog">

                                    <div class="modal-dialog modal-lg" role="document">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <h5 class="modal-title">
                                                    <i class="fas fa-calendar-check mr-2"></i>
                                                    Detail Absensi
                                                </h5>

                                                <button type="button" class="close" data-dismiss="modal">

                                                    <span>&times;</span>

                                                </button>

                                            </div>

                                            <div class="modal-body">

                                                <div class="row">

                                                    {{-- Tanggal --}}
                                                    <div class="col-md-6 mb-3">

                                                        <div class="border rounded p-3 h-100">

                                                            <small class="text-muted">
                                                                TANGGAL
                                                            </small>

                                                            <h5 class="font-weight-bold mb-0 mt-1">
                                                                {{ $tanggal->translatedFormat('d F Y') }}
                                                            </h5>

                                                            <small class="text-muted">
                                                                {{ $tanggal->translatedFormat('l') }}
                                                            </small>

                                                        </div>

                                                    </div>

                                                    {{-- Status --}}
                                                    <div class="col-md-6 mb-3">

                                                        <div class="border rounded p-3 h-100">

                                                            <small class="text-muted">
                                                                STATUS
                                                            </small>

                                                            <div class="mt-2">

                                                                @if($item->status_masuk === 'tepat_waktu')

                                                                    <span class="badge badge-success p-2">
                                                                        <i class="fas fa-check-circle mr-1"></i>
                                                                        Tepat Waktu
                                                                    </span>

                                                                @elseif($item->status_masuk === 'terlambat')

                                                                    <span class="badge badge-warning p-2">
                                                                        <i class="fas fa-clock mr-1"></i>
                                                                        Terlambat
                                                                    </span>

                                                                @else

                                                                    <span class="badge badge-secondary p-2">
                                                                        {{ ucfirst($item->status_masuk ?? '-') }}
                                                                    </span>

                                                                @endif

                                                            </div>

                                                        </div>

                                                    </div>

                                                    {{-- Jam Masuk --}}
                                                    <div class="col-md-6 mb-3">

                                                        <div class="border rounded p-3">

                                                            <small class="text-muted">
                                                                JAM MASUK
                                                            </small>

                                                            <h4 class="text-primary font-weight-bold mt-1 mb-0">

                                                                {{ $item->jam_masuk ?? '--:--' }}

                                                            </h4>

                                                        </div>

                                                    </div>

                                                    {{-- Jam Keluar --}}
                                                    <div class="col-md-6 mb-3">

                                                        <div class="border rounded p-3">

                                                            <small class="text-muted">
                                                                JAM KELUAR
                                                            </small>

                                                            <h4 class="text-success font-weight-bold mt-1 mb-0">

                                                                {{ $item->jam_keluar ?? '--:--' }}

                                                            </h4>

                                                        </div>

                                                    </div>

                                                </div>

                                                <hr>

                                                {{-- Lokasi Masuk --}}
                                                <h6 class="font-weight-bold">
                                                    <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                                                    Lokasi Saat Absen Masuk
                                                </h6>

                                                <div class="bg-light rounded p-3">

                                                    @if($item->jalan_masuk || $item->kota_masuk)

                                                        <p class="mb-1">
                                                            {{ $item->jalan_masuk ?? '-' }}
                                                        </p>

                                                        <p class="text-muted mb-0">
                                                            {{ $item->kota_masuk ?? '-' }}
                                                        </p>

                                                    @else

                                                        <span class="text-muted">
                                                            Lokasi tidak tersedia.
                                                        </span>

                                                    @endif

                                                </div>

                                                {{-- Koordinat --}}
                                                @if($item->latitude_masuk && $item->longitude_masuk)

                                                    <div class="row mt-3">

                                                        <div class="col-md-6">

                                                            <small class="text-muted">
                                                                LATITUDE
                                                            </small>

                                                            <p class="font-weight-bold mb-0">
                                                                {{ $item->latitude_masuk }}
                                                            </p>

                                                        </div>

                                                        <div class="col-md-6">

                                                            <small class="text-muted">
                                                                LONGITUDE
                                                            </small>

                                                            <p class="font-weight-bold mb-0">
                                                                {{ $item->longitude_masuk }}
                                                            </p>

                                                        </div>

                                                    </div>

                                                @endif

                                            </div>

                                            <div class="modal-footer">

                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">

                                                    <i class="fas fa-times mr-1"></i>
                                                    Tutup

                                                </button>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="text-center py-5">

                    <div class="mb-3">

                        <i class="fas fa-calendar-times text-muted" style="font-size:60px;"></i>

                    </div>

                    <h5 class="font-weight-bold">
                        Belum Ada Riwayat Absensi
                    </h5>

                    <p class="text-muted mb-3">
                        Tidak ditemukan data absensi untuk periode
                        {{ \Carbon\Carbon::createFromDate($tahunDipilih ?? now()->year, $bulan ?? now()->month, 1)->translatedFormat('F Y') }}.
                    </p>

                    <a href="{{ route('employee.attendance') }}" class="btn btn-primary">

                        <i class="fas fa-calendar-check mr-1"></i>
                        Lakukan Absensi

                    </a>

                </div>

            @endif

        </div>

        @if(isset($absensi) && $absensi->count() > 0)

            <div class="card-footer clearfix">

                <div class="float-left text-muted">
                    Menampilkan
                    <strong>{{ $absensi->count() }}</strong>
                    data absensi.
                </div>

            </div>

        @endif

    </div>

</div>

@stop