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
                            <th>Jenis Kelamin</th>
                            <th>ID Employee</th>
                            <th>Position</th>
                            <th>Vendor</th>
                            <th>Region</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>ID Number</th>
                            <th>Bank</th>
                            <th>No Rekening</th>
                            {{-- <th>Start Date</th> --}}
                            <th class="text-center">Role</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 120px;">Action</th>
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

                                {{-- jenis kelamin --}}
                                <td>{{ $item->jenis_kelamin ?? '-' }}</td>

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
                                {{-- <td>
                                    {{ $item->start_date
                                    ? \Carbon\Carbon::parse($item->start_date)->format('d/m/Y')
                                    : '-' }} --}}
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
                                {{-- ACTION --}}
                                <td class="text-center">
                                    {{-- SHOW --}}
                                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                        data-target="#modalShowKaryawan{{ $item->id }}" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- EDIT --}}
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#modalEditKaryawan{{ $item->id }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- DELETE --}}
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#modalDeleteKaryawan{{ $item->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
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


{{-- MODAL CREATE KARYAWAN --}}
<div class="modal fade" id="modalCreateKaryawan" tabindex="-1" role="dialog" aria-labelledby="modalCreateKaryawanLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('hrd.karyawan.store') }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalCreateKaryawanLabel">
                        <i class="fas fa-user-plus mr-2"></i> Tambah Karyawan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        {{-- EMPLOYEE ID --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee ID</label>
                                <input type="text" id="create_employee_id" class="form-control"
                                    value="{{ $nextEmployeeId }}" readonly>
                                <small class="text-muted">Employee ID dibuat otomatis oleh sistem.</small>
                            </div>
                        </div>

                        {{-- NAMA --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap"
                                    required>
                            </div>
                        </div>

                        {{-- POSITION --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Position</label>
                                <input type="text" name="position" class="form-control"
                                    placeholder="Masukkan posisi/jabatan">
                            </div>
                        </div>

                        {{-- VENDOR --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Vendor</label>
                                <input type="text" name="vendor" class="form-control" placeholder="Masukkan vendor">
                            </div>
                        </div>

                        {{-- REGION --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Region</label>
                                <input type="text" name="region" class="form-control" placeholder="Masukkan region">
                            </div>
                        </div>

                        {{-- JENIS KELAMIN --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        {{-- PHONE --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control"
                                    placeholder="Masukkan nomor telepon">
                            </div>
                        </div>

                        {{-- ID NUMBER --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID Number</label>
                                <input type="text" name="id_number" class="form-control"
                                    placeholder="Masukkan nomor identitas">
                            </div>
                        </div>

                        {{-- BANK --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank</label>
                                <input type="text" name="bank" class="form-control" placeholder="Masukkan nama bank">
                            </div>
                        </div>

                        {{-- NO REKENING --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No Rekening</label>
                                <input type="text" name="no_rekening" class="form-control"
                                    placeholder="Masukkan nomor rekening">
                            </div>
                        </div>

                        {{-- ALAMAT --}}
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control" rows="2"
                                    placeholder="Masukkan alamat lengkap"></textarea>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr>
                            <h6 class="font-weight-bold mb-3">
                                <i class="fas fa-lock mr-1"></i> Informasi Akun
                            </h6>
                        </div>

                        {{-- EMAIL --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Masukkan email"
                                    required>
                            </div>
                        </div>

                        {{-- PASSWORD --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control"
                                    placeholder="Minimal 8 karakter" required>
                            </div>
                        </div>

                        {{-- KONFIRMASI PASSWORD --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Ulangi password" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- =========================================================
MODAL SHOW, EDIT, DAN DELETE KARYAWAN
========================================================= --}}
@foreach($karyawan as $item)

    {{-- MODAL SHOW --}}
    <div class="modal fade" id="modalShowKaryawan{{ $item->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info">
                    <h5 class="modal-title text-white">
                        <i class="fas fa-user mr-2"></i>Detail Karyawan
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        @foreach([
                                'name' => 'Nama Karyawan',
                                'jenis_kelamin' => 'Jenis Kelamin',
                                'employee_id' => 'ID Employee',
                                'position' => 'Position',
                                'vendor' => 'Vendor',
                                'region' => 'Region',
                                'email' => 'Email',
                                'phone' => 'Phone',
                                'id_number' => 'ID Number',
                                'bank' => 'Bank',
                                'no_rekening' => 'No Rekening',
                                'role' => 'Role'
                            ] as $field => $label)
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">{{ $label }}</small>
                                <div class="font-weight-bold">{{ $item->$field ?? '-' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEditKaryawan{{ $item->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('hrd.karyawan.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">
                            <i class="fas fa-user-edit mr-2"></i>Edit Data Karyawan
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            @foreach([
                                    'name' => 'Nama Karyawan',
                                    'employee_id' => 'ID Employee',
                                    'position' => 'Position',
                                    'vendor' => 'Vendor',
                                    'region' => 'Region',
                                    'email' => 'Email',
                                    'phone' => 'Phone',
                                    'id_number' => 'ID Number',
                                    'bank' => 'Bank',
                                    'no_rekening' => 'No Rekening'
                                ] as $field => $label)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ $label }}</label>
                                        <input type="text" name="{{ $field }}" class="form-control" value="{{ $item->$field }}">
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="Laki-laki" {{ $item->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                            Laki-laki</option>
                                        <option value="Perempuan" {{ $item->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role</label>
                                    <select name="role" class="form-control">
                                        @foreach(['employee', 'admin', 'superadmin', 'hrd'] as $role)
                                            <option value="{{ $role }}" {{ $item->role == $role ? 'selected' : '' }}>
                                                {{ ucfirst($role) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save mr-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="modalDeleteKaryawan{{ $item->id }}" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('hrd.karyawan.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white">
                            <i class="fas fa-trash mr-2"></i>Hapus Karyawan
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h5>Apakah Anda yakin?</h5>
                        <p>Data karyawan berikut akan dihapus:</p>
                        <strong>{{ $item->name }}</strong>
                        <br>
                        <small class="text-muted">
                            ID Employee: {{ $item->employee_id ?? '-' }}
                        </small>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash mr-1"></i>Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach




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