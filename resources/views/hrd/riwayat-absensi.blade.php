@extends('adminlte::page')

@section('title', 'Riwayat Absensi')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="font-weight-bold mb-0">
            <i class="fas fa-history mr-2"></i> Riwayat Absensi
        </h1>
        <small class="text-muted">Seluruh aktivitas absensi karyawan MCB</small>
    </div>
    <a href="{{ route('hrd.dashboard') }}" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- FILTER RIWAYAT ABSENSI --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-filter mr-2 text-primary"></i>
                Filter Riwayat Absensi
            </h3>
        </div>

        <div class="card-body">
            <form action="{{ route('hrd.riwayat-absensi') }}" method="GET">
                <div class="row">

                    {{-- SEARCH --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama Karyawan</label>
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..."
                                value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- POSITION --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Position</label>
                            <select name="position" class="form-control">
                                <option value="">Semua Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>
                                        {{ $position }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- VENDOR --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Vendor</label>
                            <select name="vendor" class="form-control">
                                <option value="">Semua Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor }}" {{ request('vendor') == $vendor ? 'selected' : '' }}>
                                        {{ $vendor }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- REGION --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Region</label>
                            <select name="region" class="form-control">
                                <option value="">Semua Region</option>
                                @foreach($regions as $region)
                                    <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>
                                        {{ $region }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- TANGGAL --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Absensi</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
                        </div>
                    </div>

                    {{-- BUTTON --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="d-block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search mr-1"></i> Terapkan Filter
                            </button>

                            <a href="{{ route('hrd.riwayat-absensi') }}" class="btn btn-secondary">
                                <i class="fas fa-sync-alt mr-1"></i> Reset
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>


    {{-- TABEL RIWAYAT ABSENSI --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-history mr-2 text-primary"></i>
                Aktivitas Absensi Karyawan
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ $riwayat->total() }} Data
                </span>
            </div>
        </div>

        <div class="card-body table-responsive p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Karyawan</th>
                        <th>Email</th>
                        <th>Position</th>
                        <th>Vendor</th>
                        <th>Region</th>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($riwayat as $index => $absensi)
                        <tr>
                            <td>{{ $riwayat->firstItem() + $index }}</td>
                            <td>
                                <div class="font-weight-bold">
                                    {{ $absensi->name }}
                                </div>
                            </td>
                            <td>{{ $absensi->email }}</td>
                            <td>{{ $absensi->position ?? '-' }}</td>
                            <td>{{ $absensi->vendor ?? '-' }}</td>
                            <td>{{ $absensi->region ?? '-' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}
                            </td>
                            <td>{{ $absensi->jam_masuk ?? '-' }}</td>
                            <td>{{ $absensi->jam_keluar ?? '-' }}</td>
                            <td>
                                @if($absensi->status_masuk === 'terlambat')
                                    <span class="badge badge-warning">Terlambat</span>
                                @elseif($absensi->status_masuk === 'tepat_waktu')
                                    <span class="badge badge-success">Tepat Waktu</span>
                                @else
                                    <span class="badge badge-secondary">
                                        {{ $absensi->status_masuk ?? 'Belum Ada Status' }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">
                                    Tidak ada data riwayat absensi.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer clearfix">
            <div class="float-left text-muted">
                <small>
                    Menampilkan {{ $riwayat->firstItem() ?? 0 }}
                    - {{ $riwayat->lastItem() ?? 0 }}
                    dari {{ $riwayat->total() }} data.
                </small>
            </div>
            <div class="float-right">
                {{ $riwayat->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>
@stop