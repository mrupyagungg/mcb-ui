@extends('adminlte::page')

@section('title', 'Team Management')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="font-weight-bold mb-1">Team Management</h1>
        <p class="text-muted mb-0">Pengelolaan tim, anggota, dan riwayat penempatan karyawan MCB.</p>
    </div>
    <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambahTim">
        <i class="fas fa-plus mr-1"></i> Tambah Tim
    </button>
</div>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <strong>Terjadi kesalahan:</strong>
    <ul class="mb-0 mt-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- STATISTIK --}}
<div class="row">
    <div class="col-md-4">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $teams->total() }}</h3>
                <p>Total Tim (halaman saat ini)</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ \App\Models\Team::where('is_active', true)->count() }}</h3>
                <p>Tim Aktif</p>
            </div>
            <div class="icon"><i class="fas fa-sitemap"></i></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ \App\Models\TeamMember::where('status', 'aktif')->distinct('user_id')->count('user_id') }}</h3>
                <p>Karyawan dengan Penempatan Aktif</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
        </div>
    </div>
</div>

{{-- FILTER --}}
<div class="card card-outline card-primary">
    <div class="card-body">
        <form method="GET" action="{{ route('hrd.team-management.index') }}">
            <div class="row">
                <div class="col-md-7">
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                        placeholder="Cari nama tim, coordinator, atau anggota...">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status')=='aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ request('status')=='nonaktif' ? 'selected' : '' }}>Nonaktif
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- DAFTAR TIM --}}
@forelse($teams as $team)
<div class="card card-outline card-primary shadow-sm">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-users mr-2 text-primary"></i>
                    {{ $team->nama }}
                </h3>
                <div class="mt-2">
                    <small class="text-muted">
                        Penanggung Jawab:
                        <strong>{{ $team->coordinator->name ?? 'Belum ditentukan' }}</strong>
                    </small>
                    <span class="mx-2">|</span>
                    <span class="badge badge-{{ $team->is_active ? 'success' : 'secondary' }}">
                        {{ $team->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            <div class="card-tools">
                <button class="btn btn-sm btn-outline-primary" data-toggle="modal"
                    data-target="#editTim{{ $team->id }}">
                    <i class="fas fa-edit"></i> Edit
                </button>

                @if($team->is_active)
                <form action="{{ route('team-management.destroy', $team->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Nonaktifkan tim ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-power-off"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="card-body">

        @if($team->deskripsi)
        <p class="text-muted">{{ $team->deskripsi }}</p>
        @endif

        {{-- FORM TAMBAH ANGGOTA --}}
        @if($team->is_active)
        <div class="bg-light p-3 rounded mb-3">
            <h6 class="font-weight-bold mb-3">
                <i class="fas fa-user-plus mr-1 text-primary"></i>
                Tambah Anggota Tim
            </h6>

            <form action="{{ route('team-management.members.store', $team->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <label>Karyawan</label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Pilih Karyawan</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} — {{ $user->position ?? 'Tanpa Jabatan' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Region Penempatan</label>
                        <input type="text" name="region" class="form-control" placeholder="Contoh: Bekasi">
                    </div>
                    <div class="col-md-3">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ now()->toDateString() }}"
                            required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-success btn-block">
                            <i class="fas fa-plus mr-1"></i> Tambahkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif

        {{-- TABEL ANGGOTA --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="thead-light">
                    <tr>
                        <th width="45">No.</th>
                        <th>Nama Karyawan</th>
                        <th>Position</th>
                        <th>Kategori Otomatis</th>
                        <th>Region</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th width="230">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($team->members as $index => $member)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $member->user->name ?? 'Karyawan tidak ditemukan' }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $member->user->employee_id ?? '-' }}
                            </small>
                        </td>
                        <td>{{ $member->user->position ?? '-' }}</td>
                        <td>{{ $member->kategori }}</td>
                        <td>{{ $member->region ?? '-' }}</td>
                        <td>{{ $member->tanggal_mulai?->format('d/m/Y') ?? '-' }}</td>
                        <td>{{ $member->tanggal_selesai?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $member->status == 'aktif' ? 'success' : 'secondary' }}">
                                {{ ucfirst($member->status) }}
                            </span>
                        </td>
                        <td>
                            @if($member->status == 'aktif')
                            <button class="btn btn-xs btn-info" data-toggle="modal"
                                data-target="#pindah{{ $member->id }}">
                                <i class="fas fa-exchange-alt"></i> Pindah
                            </button>

                            <form action="{{ route('team-management.members.remove', $member->id) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Nonaktifkan keanggotaan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-xs btn-danger">
                                    <i class="fas fa-user-minus"></i> Keluarkan
                                </button>
                            </form>
                            @else
                            <span class="text-muted">Riwayat penempatan</span>
                            @endif
                        </td>
                    </tr>

                    {{-- MODAL PINDAH TIM --}}
                    @if($member->status == 'aktif')
                    <div class="modal fade" id="pindah{{ $member->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('team-management.members.transfer', $member->id) }}" method="POST"
                                class="modal-content">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Pindahkan Anggota Tim</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p>
                                        Karyawan:
                                        <strong>{{ $member->user->name ?? '-' }}</strong>
                                    </p>

                                    <div class="form-group">
                                        <label>Tim Tujuan</label>
                                        <select name="target_team_id" class="form-control" required>
                                            <option value="">Pilih Tim Tujuan</option>
                                            @foreach($allTeams->where('id', '!=', $team->id) as $targetTeam)
                                            <option value="{{ $targetTeam->id }}">
                                                {{ $targetTeam->nama }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Region Baru</label>
                                        <input type="text" name="region" class="form-control"
                                            value="{{ $member->region }}">
                                    </div>

                                    <div class="form-group">
                                        <label>Tanggal Mulai di Tim Baru</label>
                                        <input type="date" name="tanggal_mulai" class="form-control"
                                            value="{{ now()->toDateString() }}" required>
                                    </div>

                                    <small class="text-muted">
                                        Hanya keanggotaan di tim ini yang ditutup. Keanggotaan aktif
                                        karyawan pada tim lainnya tidak akan diubah.
                                    </small>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                        Batal
                                    </button>
                                    <button class="btn btn-primary">Pindahkan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            Belum ada anggota dalam tim ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL EDIT TIM --}}
<div class="modal fade" id="editTim{{ $team->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('team-management.update', $team->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title">Edit Tim</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Tim</label>
                    <input type="text" name="nama" class="form-control" value="{{ $team->nama }}" required>
                </div>

                <div class="form-group">
                    <label>Penanggung Jawab</label>
                    <select name="coordinator_id" class="form-control">
                        <option value="">Belum Ditentukan</option>
                        @foreach($coordinators as $coordinator)
                        <option value="{{ $coordinator->id }}" {{ $team->coordinator_id == $coordinator->id ? 'selected'
                            : '' }}>
                            {{ $coordinator->name }} — {{ $coordinator->position }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ $team->deskripsi }}</textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@empty
<div class="card">
    <div class="card-body text-center text-muted">
        Belum ada data tim.
    </div>
</div>
@endforelse

<div class="d-flex justify-content-end">
    {{ $teams->links() }}
</div>

{{-- MODAL TAMBAH TIM --}}
<div class="modal fade" id="modalTambahTim" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('hrd.team-management.store') }}" method="POST" class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Tambah Tim Baru</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Tim</label>
                    <input type="text" name="nama" class="form-control" placeholder="Contoh: Tim Bekasi 1" required>
                </div>

                <div class="form-group">
                    <label>Penanggung Jawab</label>
                    <select name="coordinator_id" class="form-control">
                        <option value="">Belum Ditentukan</option>
                        @foreach($coordinators as $coordinator)
                        <option value="{{ $coordinator->id }}">
                            {{ $coordinator->name }} — {{ $coordinator->position }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Tim
                </button>
            </div>
        </form>
    </div>
</div>

@stop