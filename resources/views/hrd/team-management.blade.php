    @extends('adminlte::page')

    @section('title', 'Team Management')
    @section('plugins.Sweetalert2', true)
    @section('content_header')
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="font-weight-bold mb-1">
                    <i class="fas fa-sitemap text-primary mr-2"></i>Team Management
                </h1>
                <p class="text-muted mb-0">
                    Struktur tim, penanggung jawab, dan anggota karyawan MCB.
                </p>
            </div>
            <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#modalTambahTim">
                <i class="fas fa-plus mr-1"></i> Tambah Tim
            </button>
        </div>
    @stop

    @section('content')



        {{-- STATISTIK --}}
        <div class="row">
            <div class="col-lg-4 col-md-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{ $teams->total() }}</h3>
                        <p>Total Tim</p>
                    </div>
                    <div class="icon"><i class="fas fa-sitemap"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $totalTimAktif }}</h3>
                        <p>Tim Aktif</p>
                    </div>
                    <div class="icon"><i class="fas fa-users"></i></div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $totalKaryawanAktif }}</h3>
                        <p>Karyawan dengan Penempatan Aktif</p>
                    </div>
                    <div class="icon"><i class="fas fa-user-check"></i></div>
                </div>
            </div>
        </div>

        {{-- STRUKTUR ORGANISASI --}}
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-project-diagram text-primary mr-2"></i>
                    Struktur Organisasi Tim
                </h3>
            </div>

            <div class="card-body">

                <p class="text-muted mb-3">
                    Struktur penempatan karyawan berdasarkan penanggung jawab tim.
                    Klik tombol panah untuk melihat anggota di bawahnya.
                </p>

                {{-- TOMBOL BUKA TUTUP --}}
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-default border" id="btnBukaSemua">
                        <i class="fas fa-expand-alt mr-1"></i> Buka Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-default border" id="btnTutupSemua">
                        <i class="fas fa-compress-alt mr-1"></i> Tutup Semua
                    </button>
                </div>

                {{-- FILTER --}}
                <form method="GET" action="{{ route('hrd.team-management.index') }}" class="mb-3">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <div class="form-group mb-md-0">
                                <label>Cari Karyawan / Tim</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                    </div>
                                    <input type="text" name="search" class="form-control"
                                        value="{{ request('search') }}"
                                        placeholder="Nama, Employee ID, jabatan, atau nama tim...">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mt-3 mt-md-0">
                            <div class="form-group mb-0">
                                <label>Status Tim</label>
                                <select name="status" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>
                                        Nonaktif
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 mt-3 mt-md-0">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>

                {{-- RINGKASAN --}}
                <div class="mb-3">
                    <span class="badge badge-primary p-2 mr-1">
                        {{ $teams->total() }} Tim
                    </span>
                    <span class="badge badge-success p-2 mr-1">
                        {{ $totalTimAktif }} Tim Aktif
                    </span>
                    <span class="badge badge-info p-2">
                        {{ $totalKaryawanAktif }} Penempatan Aktif
                    </span>
                </div>

                {{-- POHON STRUKTUR --}}
                <div class="organization-tree">

                    @forelse ($teams as $team)
                        @php
                            $activeMembers = $team->members->where('status', 'aktif');
                            $historyMembers = $team->members->where('status', 'nonaktif');
                            $coordinatorName = $team->coordinator->name ?? 'Belum ditentukan';
                            $initial = strtoupper(substr($coordinatorName, 0, 1));
                        @endphp

                        <div class="org-team mb-3" data-team-id="{{ $team->id }}">

                            {{-- NODE PENANGGUNG JAWAB --}}
                            <div class="org-node org-parent">

                                <button type="button" class="org-toggle" data-target="teamChildren{{ $team->id }}"
                                    aria-expanded="true">
                                    <i class="fas fa-chevron-down"></i>
                                </button>

                                <div class="org-avatar bg-primary">
                                    {{ $initial }}
                                </div>

                                <div class="org-info">
                                    <div class="org-name">
                                        {{ $coordinatorName }}
                                        @if ($team->coordinator)
                                            <span class="badge badge-primary ml-1">
                                                {{ $team->coordinator->position ?? 'Penanggung Jawab' }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="org-description">
                                        {{ $team->nama }}
                                        <span class="mx-1">•</span>
                                        {{ $activeMembers->count() }} anggota aktif
                                        @if (!$team->is_active)
                                            <span class="badge badge-secondary ml-1">Tim Nonaktif</span>
                                        @else
                                            <span class="badge badge-success ml-1">Aktif</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="org-meta d-none d-md-block">
                                    <span class="text-muted small">
                                        {{ $activeMembers->count() }} langsung
                                        • {{ $team->members->count() }} riwayat
                                    </span>
                                </div>

                                <div class="org-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                        data-target="#modalTambahAnggota{{ $team->id }}">
                                        <i class="fas fa-sitemap mr-1"></i> Atur Tim
                                    </button>

                                    <button type="button" class="btn btn-sm btn-default border" data-toggle="modal"
                                        data-target="#editTim{{ $team->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    @if ($team->is_active)
                                        <form action="{{ route('hrd.team-management.destroy', $team->id) }}"
                                            method="POST" class="d-inline form-konfirmasi" data-title="Nonaktifkan Tim?"
                                            data-text="Tim {{ $team->nama }} akan dinonaktifkan."
                                            data-confirm="Ya, Nonaktifkan">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('hrd.team-management.activate', $team->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Aktifkan kembali tim ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('hrd.team-management.destroy', $team->id) }}" method="POST"
                                        class="d-inline form-hapus-tim" data-nama="{{ $team->nama }}">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash mr-1"></i> Hapus Tim
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- ANGGOTA DI BAWAH PENANGGUNG JAWAB --}}
                            <div class="org-children" id="teamChildren{{ $team->id }}">

                                @forelse ($activeMembers as $member)
                                    <div class="org-member-row">

                                        <div class="org-connector"></div>

                                        <div class="org-node org-child">
                                            <div class="org-avatar org-avatar-member">
                                                {{ strtoupper(substr($member->user->name ?? '?', 0, 1)) }}
                                            </div>

                                            <div class="org-info">
                                                <div class="org-name">
                                                    {{ $member->user->name ?? 'Karyawan tidak ditemukan' }}
                                                </div>
                                                <div class="org-description">
                                                    {{ $member->user->employee_id ?? 'Tanpa ID' }}
                                                    <span class="mx-1">•</span>
                                                    {{ $member->user->position ?? '-' }}
                                                    @if ($member->region)
                                                        <span class="mx-1">•</span>{{ $member->region }}
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="org-meta d-none d-md-block">
                                                <span class="badge badge-success">Aktif</span>
                                                <span class="text-muted small ml-1">
                                                    Sejak {{ $member->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                                                </span>
                                            </div>

                                            <div class="org-actions">
                                                @if ($team->is_active)
                                                    <button type="button" class="btn btn-xs btn-info"
                                                        data-toggle="modal" data-target="#pindah{{ $member->id }}">
                                                        <i class="fas fa-exchange-alt"></i>
                                                        <span class="d-none d-sm-inline">Pindah</span>
                                                    </button>

                                                    <form
                                                        action="{{ route('hrd.team-management.members.remove', $member->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Keluarkan anggota ini dari tim?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-xs btn-outline-danger">
                                                            <i class="fas fa-user-minus"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted small">
                                                        <i class="fas fa-lock"></i> Tim nonaktif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="org-member-row">
                                        <div class="org-connector"></div>
                                        <div class="org-empty">
                                            <i class="fas fa-user-slash mr-1"></i>
                                            Belum ada anggota aktif pada tim ini.
                                        </div>
                                    </div>
                                @endforelse

                                {{-- RIWAYAT ANGGOTA NONAKTIF --}}
                                @if ($historyMembers->count())
                                    <div class="org-history">
                                        <button type="button" class="btn btn-sm btn-link text-muted p-0"
                                            data-toggle="collapse" data-target="#history{{ $team->id }}">
                                            <i class="fas fa-history mr-1"></i>
                                            Riwayat Penempatan ({{ $historyMembers->count() }})
                                            <i class="fas fa-chevron-down ml-1"></i>
                                        </button>

                                        <div class="collapse mt-2" id="history{{ $team->id }}">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered bg-white">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>Nama Karyawan</th>
                                                            <th>Employee ID</th>
                                                            <th>Position</th>
                                                            <th>Tanggal Mulai</th>
                                                            <th>Tanggal Selesai</th>
                                                            <th>Status</th>
                                                            <th width="90">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($historyMembers as $member)
                                                            <tr>
                                                                <td>{{ $member->user->name ?? '-' }}</td>
                                                                <td>{{ $member->user->employee_id ?? '-' }}</td>
                                                                <td>{{ $member->user->position ?? '-' }}</td>
                                                                <td>{{ $member->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                                                                </td>
                                                                <td>{{ $member->tanggal_selesai?->format('d/m/Y') ?? '-' }}
                                                                </td>
                                                                <td><span class="badge badge-secondary">Nonaktif</span>
                                                                </td>
                                                                <td>
                                                                    <form
                                                                        action="{{ route('hrd.team-management.members.destroy', $member->id) }}"
                                                                        method="POST" class="d-inline form-konfirmasi"
                                                                        data-title="Hapus Riwayat Permanen?"
                                                                        data-text="Riwayat penempatan ini akan dihapus permanen dari database."
                                                                        data-confirm="Ya, Hapus Permanen">
                                                                        @csrf
                                                                        @method('DELETE')

                                                                        <button type="submit"
                                                                            class="btn btn-xs btn-danger">
                                                                            <i class="fas fa-trash mr-1"></i> Hapus
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- MODAL TAMBAH ANGGOTA --}}
                        <div class="modal fade" id="modalTambahAnggota{{ $team->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <form action="{{ route('hrd.team-management.members.store', $team->id) }}" method="POST"
                                    class="modal-content">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-user-plus text-primary mr-1"></i>
                                            Atur Anggota Tim - {{ $team->nama }}
                                        </h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @if ($team->is_active)
                                            <div class="form-group">
                                                <label>Pilih Karyawan</label>
                                                <select name="user_id" class="form-control" required>
                                                    <option value="">Pilih Karyawan</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">
                                                            {{ $user->employee_id ?? 'Tanpa ID' }}
                                                            — {{ $user->name }}
                                                            — {{ $user->position ?? '-' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" name="tanggal_mulai" class="form-control"
                                                    value="{{ now()->toDateString() }}" required>
                                            </div>
                                        @else
                                            <div class="alert alert-warning mb-0">
                                                Tim ini sedang nonaktif. Aktifkan kembali tim sebelum menambahkan anggota.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Tutup</button>
                                        @if ($team->is_active)
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save mr-1"></i> Tambahkan Anggota
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- MODAL EDIT TIM --}}
                        <div class="modal fade" id="editTim{{ $team->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('hrd.team-management.update', $team->id) }}" method="POST"
                                    class="modal-content">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title font-weight-bold">Edit Tim</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Nama Tim</label>
                                            <input type="text" name="nama" class="form-control"
                                                value="{{ $team->nama }}" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Penanggung Jawab</label>
                                            <select name="coordinator_id" class="form-control">
                                                <option value="">Belum Ditentukan</option>
                                                @foreach ($coordinators as $coordinator)
                                                    <option value="{{ $coordinator->id }}"
                                                        {{ $team->coordinator_id == $coordinator->id ? 'selected' : '' }}>
                                                        {{ $coordinator->name }} — {{ $coordinator->position }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-0">
                                            <label>Deskripsi</label>
                                            <textarea name="deskripsi" class="form-control" rows="3">{{ $team->deskripsi }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- MODAL PINDAH ANGGOTA --}}
                        @if ($team->is_active)
                            @foreach ($activeMembers as $member)
                                @php
                                    $tanggalMinimum = $member->tanggal_mulai
                                        ? $member->tanggal_mulai->copy()->addDay()->toDateString()
                                        : now()->toDateString();
                                    $tanggalPindah = max(now()->toDateString(), $tanggalMinimum);
                                @endphp

                                <div class="modal fade" id="pindah{{ $member->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('hrd.team-management.members.transfer', $member->id) }}"
                                            method="POST" class="modal-content">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title font-weight-bold">
                                                    <i class="fas fa-exchange-alt text-info mr-1"></i>
                                                    Pindahkan Anggota
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="bg-light border rounded p-3 mb-3">
                                                    <strong>{{ $member->user->name ?? '-' }}</strong>
                                                    <div class="text-muted small">
                                                        {{ $member->user->employee_id ?? '-' }}
                                                        — {{ $member->user->position ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>Tim Tujuan</label>
                                                    <select name="target_team_id" class="form-control" required>
                                                        <option value="">Pilih Tim Tujuan</option>
                                                        @foreach ($allTeams->where('id', '!=', $team->id)->where('is_active', true) as $targetTeam)
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
                                                <div class="form-group mb-0">
                                                    <label>Tanggal Mulai di Tim Baru</label>
                                                    <input type="date" name="tanggal_mulai" class="form-control"
                                                        value="{{ $tanggalPindah }}" min="{{ $tanggalMinimum }}"
                                                        required>
                                                </div>
                                                <div class="alert alert-info mt-3 mb-0">
                                                    Hanya keanggotaan pada tim ini yang ditutup. Keanggotaan aktif di tim
                                                    lainnya tidak berubah.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-exchange-alt mr-1"></i> Pindahkan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-sitemap fa-3x mb-3"></i>
                            <h5>Belum Ada Struktur Tim</h5>
                            <p>Silakan tambahkan tim terlebih dahulu.</p>
                        </div>
                    @endforelse
                </div>

                {{-- PAGINATION --}}
                <div class="d-flex justify-content-end mt-3">
                    {{ $teams->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL TAMBAH TIM --}}
        <div class="modal fade" id="modalTambahTim" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('hrd.team-management.store') }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-plus-circle text-primary mr-1"></i> Tambah Tim Baru
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Tim</label>
                            <input type="text" name="nama" class="form-control" placeholder="Contoh: Tim Bekasi 1"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Penanggung Jawab</label>
                            <select name="coordinator_id" class="form-control">
                                <option value="">Belum Ditentukan</option>
                                @foreach ($coordinators as $coordinator)
                                    <option value="{{ $coordinator->id }}">
                                        {{ $coordinator->name }} — {{ $coordinator->position }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Tim
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @stop

    @section('css')
        <style>
            .organization-tree {
                overflow-x: auto;
                padding: 5px 2px 15px;
            }

            .org-team {
                min-width: 650px;
            }

            .org-node {
                display: flex;
                align-items: center;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: 9px;
                padding: 12px 14px;
                transition: all .2s ease;
                gap: 12px;
            }

            .org-node:hover {
                border-color: #80aef5;
                box-shadow: 0 3px 12px rgba(0, 0, 0, .06);
            }

            .org-parent {
                background: #fbfdff;
                border-left: 4px solid #007bff;
            }

            .org-child {
                margin-left: 35px;
                border-left: 3px solid #d5e1ef;
            }

            .org-toggle {
                border: 0;
                background: transparent;
                color: #64748b;
                width: 22px;
                cursor: pointer;
                padding: 3px;
            }

            .org-toggle:hover {
                color: #007bff;
            }

            .org-avatar {
                width: 38px;
                height: 38px;
                min-width: 38px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                font-weight: 700;
                font-size: 15px;
            }

            .org-avatar-member {
                background: #e8f0fe;
                color: #2563eb;
            }

            .org-info {
                flex: 1;
                min-width: 150px;
            }

            .org-name {
                font-weight: 700;
                color: #263445;
                font-size: 14px;
            }

            .org-description {
                color: #8492a6;
                font-size: 12px;
                margin-top: 3px;
            }

            .org-meta {
                white-space: nowrap;
                margin-right: 10px;
            }

            .org-actions {
                display: flex;
                align-items: center;
                gap: 5px;
                flex-wrap: wrap;
            }

            .org-children {
                margin-left: 28px;
                padding: 10px 0 5px 22px;
                border-left: 2px solid #dce5ef;
            }

            .org-member-row {
                position: relative;
                margin-bottom: 9px;
            }

            .org-connector {
                position: absolute;
                width: 22px;
                height: 50%;
                left: -24px;
                top: 0;
                border-bottom: 2px solid #dce5ef;
                border-left: 2px solid #dce5ef;
                border-bottom-left-radius: 7px;
            }

            .org-empty {
                margin-left: 35px;
                padding: 12px;
                color: #94a3b8;
                font-size: 13px;
                background: #f8fafc;
                border-radius: 7px;
            }

            .org-history {
                margin: 12px 0 5px 35px;
                padding-top: 10px;
                border-top: 1px dashed #dce5ef;
            }

            .org-team.collapsed .org-children {
                display: none;
            }

            .org-toggle[aria-expanded="false"] i {
                transform: rotate(-90deg);
            }

            .org-toggle i {
                transition: transform .2s ease;
            }

            @media (max-width: 767px) {
                .org-team {
                    min-width: 600px;
                }

                .org-actions .btn {
                    margin-bottom: 3px;
                }
            }
        </style>
    @stop

    @section('js')
        <script>
            $(function() {
                // BUKA / TUTUP SATU TIM
                $('.org-toggle').on('click', function() {
                    const target = $(this).data('target');
                    const team = $('#' + target).closest('.org-team');
                    const isExpanded = $(this).attr('aria-expanded') === 'true';

                    team.toggleClass('collapsed', isExpanded);
                    $(this).attr('aria-expanded', !isExpanded);
                });

                // BUKA SEMUA TIM
                $('#btnBukaSemua').on('click', function() {
                    $('.org-team').removeClass('collapsed');
                    $('.org-toggle').attr('aria-expanded', 'true');
                });

                // TUTUP SEMUA TIM
                $('#btnTutupSemua').on('click', function() {
                    $('.org-team').addClass('collapsed');
                    $('.org-toggle').attr('aria-expanded', 'false');
                });

                // NOTIFIKASI BERHASIL
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: @json(session('success')),
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'OK'
                    });
                @endif

                // NOTIFIKASI GAGAL
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: @json(session('error')),
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: 'Tutup'
                    });
                @endif

                // NOTIFIKASI VALIDASI
                @if ($errors->any())
                    Swal.fire({
                        icon: 'warning',
                        title: 'Periksa Kembali Data!',
                        html: `{!! collect($errors->all())->map(fn($error) => '<div class="text-left mb-1">• ' . e($error) . '</div>')->implode('') !!}`,
                        confirmButtonColor: '#007bff',
                        confirmButtonText: 'Mengerti'
                    });
                @endif

                // KONFIRMASI SEMUA FORM
                $(document).on('submit', '.form-konfirmasi', function(e) {
                    e.preventDefault();

                    const form = this;
                    const title = $(form).data('title') || 'Apakah Anda yakin?';
                    const message = $(form).data('text') || 'Tindakan ini akan diproses.';
                    const confirmText = $(form).data('confirm') || 'Ya, Lanjutkan';
                    const icon = $(form).data('icon') || 'warning';

                    Swal.fire({
                        title: title,
                        text: message,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: confirmText,
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        focusCancel: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @stop

    @section('js')
        <script>
            $(function() {
                // BUKA / TUTUP SATU TIM
                $('.org-toggle').on('click', function() {
                    const target = $(this).data('target');
                    const team = $('#' + target).closest('.org-team');
                    const isExpanded = $(this).attr('aria-expanded') === 'true';

                    team.toggleClass('collapsed', isExpanded);
                    $(this).attr('aria-expanded', !isExpanded);
                });

                // BUKA SEMUA TIM
                $('#btnBukaSemua').on('click', function() {
                    $('.org-team').removeClass('collapsed');
                    $('.org-toggle').attr('aria-expanded', 'true');
                });

                // TUTUP SEMUA TIM
                $('#btnTutupSemua').on('click', function() {
                    $('.org-team').addClass('collapsed');
                    $('.org-toggle').attr('aria-expanded', 'false');
                });
            });
            $(document).on('submit', '.form-hapus-tim', function(e) {
                e.preventDefault();

                const form = this;
                const namaTim = $(form).data('nama');

                Swal.fire({
                    title: 'Hapus Tim Permanen?',
                    html: `
            Anda akan menghapus tim <strong>${namaTim}</strong>
            beserta seluruh anggota dan riwayat penempatannya.
            <br><br>
            <strong class="text-danger">Data yang dihapus tidak dapat dikembalikan!</strong>
        `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Permanen',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        </script>

    @stop
