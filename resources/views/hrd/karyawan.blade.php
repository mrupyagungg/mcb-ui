@extends('adminlte::page')

@section('title', 'Data Karyawan')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="font-weight-bold mb-0">Data Karyawan</h1>
        <small class="text-muted">Manajemen dan informasi seluruh karyawan MCB</small>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- NOTIFIKASI SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <strong>
                <i class="fas fa-exclamation-circle mr-1"></i>
                Terjadi Kesalahan
            </strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- KREDENSIAL HASIL IMPORT --}}
    @if(session('kredensial_import'))
        <div class="card card-outline card-success shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-key mr-2"></i>
                    Akun Login Karyawan Hasil Import
                </h3>
            </div>

            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Simpan informasi akun ini sekarang.
                    Password hanya ditampilkan pada hasil import ini.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama</th>
                                <th>Email Login</th>
                                <th>Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('kredensial_import') as $akun)
                                <tr>
                                    <td>{{ $akun['name'] }}</td>
                                    <td>{{ $akun['email'] }}</td>
                                    <td>
                                        <code>{{ $akun['password'] }}</code>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- INFORMASI TOTAL --}}
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="info-box shadow-sm">
                <span class="info-box-icon bg-info">
                    <i class="fas fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Karyawan</span>
                    <span class="info-box-number">
                        {{ number_format($karyawan->total()) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD DAFTAR KARYAWAN --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-address-book mr-2 text-primary"></i>
                Daftar Karyawan
            </h3>

            <div class="card-tools mt-2 mt-md-0">
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                    data-target="#modalImportKaryawan">
                    <i class="fas fa-file-excel mr-1"></i>
                    Import Excel
                </button>

                <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                    data-target="#modalCreateKaryawan">
                    <i class="fas fa-user-plus mr-1"></i>
                    Create Data Karyawan
                </button>
            </div>
        </div>

        <div class="card-body">

            {{-- SEARCH --}}
            <form action="{{ route('hrd.karyawan') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-5 col-sm-8">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari nama atau email karyawan..." value="{{ request('search') }}">

                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-4 mt-2 mt-md-0">
                        <a href="{{ route('hrd.karyawan') }}" class="btn btn-secondary">
                            <i class="fas fa-sync-alt mr-1"></i>
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- TABEL KARYAWAN --}}

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Karyawan</th>
                            <th>ID Employee</th>
                            <th>Position</th>
                            <th>Vendor</th>
                            <th>Region</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>ID Number</th>
                            <th>Bank</th>
                            <th>No Rekening</th>
                            <th>Start Date</th>
                            <th class="text-center">Role</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($karyawan as $index => $item)
                                            <tr>
                                                {{-- NOMOR --}}
                                                <td class="text-center">
                                                    {{ $karyawan->firstItem() + $index }}
                                                </td>

                                                {{-- NAMA --}}
                                                <td style="min-width: 200px;">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-2">
                                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                                style="width: 38px; height: 38px; font-weight: bold;">
                                                                {{ strtoupper(substr($item->name ?? '-', 0, 1)) }}
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $item->name ?? '-' }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                ID: {{ $item->id }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- ID EMPLOYEE --}}
                                                <td>{{ $item->employee_id ?? '-' }}</td>

                                                {{-- POSITION --}}
                                                <td>{{ $item->position ?? '-' }}</td>

                                                {{-- VENDOR --}}
                                                <td>{{ $item->vendor ?? '-' }}</td>

                                                {{-- region --}}
                                                <td>{{ $item->region ?? '-' }}</td>

                                                {{-- EMAIL --}}
                                                <td style="min-width: 200px;">
                                                    <i class="far fa-envelope text-muted mr-1"></i>
                                                    {{ $item->email ?? '-' }}
                                                </td>

                                                {{-- PHONE --}}
                                                <td>{{ $item->phone ?? '-' }}</td>

                                                {{-- ID NUMBER --}}
                                                <td>{{ $item->id_number ?? '-' }}</td>

                                                {{-- BANK --}}
                                                <td>{{ $item->bank ?? '-' }}</td>

                                                {{-- NO REKENING --}}
                                                <td>{{ $item->no_rekening ?? '-' }}</td>

                                                {{-- START DATE --}}
                                                <td>
                                                    {{ $item->start_date
                            ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y')
                            : '-' }}
                                                </td>
                                                {{-- ROLE --}}
                                                <td class="text-center">
                                                    <span class="badge badge-info">
                                                        {{ ucfirst($item->role ?? '-') }}
                                                    </span>
                                                </td>

                                                {{-- STATUS --}}
                                                <td class="text-center">
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle mr-1"></i>
                                                        Terdaftar
                                                    </span>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center py-5">
                                    <i class="fas fa-users-sl fa-3x text-muted mb-3"></i>

                                    <h5 class="text-muted">
                                        Data Karyawan Tidak Ditemukan
                                    </h5>

                                    <p class="text-muted mb-0">
                                        Belum ada karyawan atau pencarian tidak menghasilkan data.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>


            {{-- PAGINATION --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-3">
                <small class="text-muted mb-2">
                    Menampilkan
                    {{ $karyawan->firstItem() ?? 0 }}
                    sampai
                    {{ $karyawan->lastItem() ?? 0 }}
                    dari
                    {{ $karyawan->total() }} karyawan
                </small>

                <div>
                    {{ $karyawan->links('pagination::bootstrap-4') }}
                </div>
            </div>

        </div>
    </div>

</div>

{{-- =========================================================
MODAL IMPORT EXCEL
========================================================= --}}
<div class="modal fade" id="modalImportKaryawan" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-info">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-file-excel mr-2"></i>
                    Import Data Karyawan
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM IMPORT --}}
            <form action="{{ route('hrd.karyawan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Upload file Excel dengan sheet
                        <strong>List Karyawan MCB</strong>.
                        Data mulai dibaca dari baris ke-4.
                    </div>

                    <div class="form-group">
                        <label for="file_excel">File Excel</label>
                        <input type="file" name="file_excel" id="file_excel"
                            class="form-control @error('file_excel') is-invalid @enderror" accept=".xlsx,.xls" required>

                        @error('file_excel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <small class="text-muted">
                            Format .xlsx atau .xls, maksimal 10 MB.
                        </small>
                    </div>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </button>

                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-upload mr-1"></i>
                        Import Sekarang
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- =========================================================
MODAL CREATE DATA KARYAWAN
========================================================= --}}
<div class="modal fade" id="modalCreateKaryawan" tabindex="-1" role="dialog" aria-labelledby="modalCreateKaryawanLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
        <div class="modal-content">

            {{-- HEADER --}}
            <div class="modal-header bg-primary">
                <h5 class="modal-title font-weight-bold" id="modalCreateKaryawanLabel">
                    <i class="fas fa-user-plus mr-2"></i>
                    Create Data Karyawan
                </h5>

                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            {{-- FORM CREATE --}}
            <form action="{{ route('hrd.karyawan.store') }}" method="POST">
                @csrf

                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i>
                        Akun karyawan akan dibuat dengan role
                        <strong>Employee</strong> secara otomatis.
                    </div>

                    {{-- NAMA --}}
                    <div class="form-group">
                        <label for="name">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>

                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Masukkan nama lengkap karyawan" value="{{ old('name') }}" required>

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- EMAIL --}}
                    <div class="form-group">
                        <label for="email">
                            Email <span class="text-danger">*</span>
                        </label>

                        <input type="email" name="email" id="email"
                            class="form-control @error('email') is-invalid @enderror" placeholder="contoh@email.com"
                            value="{{ old('email') }}" required>

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div class="form-group">
                        <label for="password">
                            Password <span class="text-danger">*</span>
                        </label>

                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Minimal 8 karakter" required>

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- KONFIRMASI PASSWORD --}}
                    <div class="form-group">
                        <label for="password_confirmation">
                            Konfirmasi Password <span class="text-danger">*</span>
                        </label>

                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="form-control" placeholder="Masukkan ulang password" required>
                    </div>

                    <small class="text-muted">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Password akan disimpan dalam bentuk terenkripsi.
                    </small>
                </div>

                {{-- FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>
                        Buat Karyawan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@stop

{{-- CSS --}}
@section('css')
<style>
    .card {
        border-radius: 8px;
    }

    .info-box {
        border-radius: 8px;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }

    .table thead th {
        border-top: none;
        white-space: nowrap;
    }

    .pagination {
        margin-bottom: 0;
    }

    #modalCreateKaryawan .modal-content,
    #modalImportKaryawan .modal-content {
        border-radius: 10px;
        overflow: hidden;
    }

    #modalCreateKaryawan .modal-header,
    #modalImportKaryawan .modal-header {
        border-bottom: none;
    }

    #modalCreateKaryawan .modal-footer,
    #modalImportKaryawan .modal-footer {
        border-top: 1px solid #dee2e6;
    }
</style>
@stop

{{-- JAVASCRIPT --}}
@section('js')
<script>
    $(document).ready(function () {
        // Jika error berasal dari Import Excel, buka modal Import
        @if($errors->has('file_excel'))
            $('#modalImportKaryawan').modal('show');

            // Jika error berasal dari Create Karyawan, buka modal Create
        @elseif($errors->has('name') || $errors->has('email') || $errors->has('password'))
            $('#modalCreateKaryawan').modal('show');
        @endif
    });
</script>
@stop