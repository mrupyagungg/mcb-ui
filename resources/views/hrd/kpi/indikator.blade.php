@extends('adminlte::page')

@section('title', 'Pengaturan Indikator KPI')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="font-weight-bold mb-1">Pengaturan Indikator KPI</h1>
            <p class="text-muted mb-0">Kelola indikator dan bobot penilaian karyawan MCB.</p>
        </div>
    </div>
@stop

@section('content')

    @php
        $kategoriLabel = [
            'dokumen' => 'Dokumen',
            'drafter' => 'Drafter',
            'permit' => 'Permit',
            'waspang' => 'Supervisor',
            'logistic' => 'Logistic',
            'admin' => 'Admin',
        ];

        $kategoriColor = [
            'dokumen' => 'primary',
            'drafter' => 'info',
            'permit' => 'warning',
            'waspang' => 'success',
            'logistic' => 'danger',
            'admin' => 'secondary',
        ];

        $kategoriIcon = [
            'dokumen' => 'fa-file-alt',
            'drafter' => 'fa-drafting-compass',
            'permit' => 'fa-file-signature',
            'waspang' => 'fa-hard-hat',
            'logistic' => 'fa-truck',
            'admin' => 'fa-user-cog',
        ];
    @endphp

    {{-- NOTIFIKASI --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <strong><i class="fas fa-exclamation-circle mr-1"></i> Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- RINGKASAN BOBOT --}}
    <div class="row">
        @foreach ($kategoriLabel as $key => $label)
            @php
                $total = (float) ($totalBobot[$key] ?? 0);
                $sisa = max(0, 100 - $total);
            @endphp

            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="small-box bg-white shadow-sm border">
                    <div class="inner">
                        <h3 class="text-{{ $kategoriColor[$key] }}">
                            {{ number_format($total, 0) }}<sup style="font-size: 16px">%</sup>
                        </h3>
                        <p class="font-weight-bold mb-2">{{ $label }}</p>

                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $kategoriColor[$key] }}" style="width: {{ min(100, $total) }}%">
                            </div>
                        </div>

                        <small class="text-muted">
                            Sisa {{ number_format($sisa, 2) }}%
                        </small>
                    </div>
                    <div class="icon">
                        <i class="fas {{ $kategoriIcon[$key] }} text-{{ $kategoriColor[$key] }}" style="opacity: .15"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- FORM TAMBAH INDIKATOR --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-plus-circle mr-1"></i>
                Tambah Indikator KPI
            </h3>
        </div>

        <form action="{{ route('hrd.kpi.indikator.store') }}" method="POST">
            @csrf

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Indikator</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}"
                                placeholder="Contoh: Ketepatan waktu penyelesaian" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Kategori Pekerjaan</label>
                            <select name="kategori" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoriLabel as $key => $label)
                                    <option value="{{ $key }}" {{ old('kategori') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Jenis Penilaian</label>
                            <select name="jenis" class="form-control" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="target">Berdasarkan Target</option>
                                <option value="rubrik">Berdasarkan Rubrik</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bobot (%)</label>
                            <input type="number" name="bobot" class="form-control" value="{{ old('bobot') }}"
                                min="0.01" max="100" step="0.01" placeholder="Contoh: 30" required>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="form-group">
                            <label>Deskripsi / Keterangan</label>
                            <input type="text" name="deskripsi" class="form-control" value="{{ old('deskripsi') }}"
                                placeholder="Jelaskan apa yang akan dinilai">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" value="1" class="custom-control-input"
                                id="statusTambah" checked>
                            <label class="custom-control-label" for="statusTambah">
                                Aktifkan indikator
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Simpan Indikator
                </button>
            </div>
        </form>
    </div>

    {{-- DAFTAR INDIKATOR --}}
    <div class="card shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-list mr-1"></i>
                Daftar Indikator KPI
            </h3>
            <div class="card-tools">
                <span class="badge badge-primary">
                    {{ $indikators->count() }} Indikator
                </span>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover table-striped">
                <thead class="bg-light">
                    <tr>
                        <th width="45">No</th>
                        <th>Indikator</th>
                        <th width="130">Kategori</th>
                        <th width="160">Jenis</th>
                        <th width="100" class="text-center">Bobot</th>
                        <th width="100" class="text-center">Status</th>
                        <th width="110" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($indikators as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                <strong>{{ $item->nama }}</strong>
                                @if ($item->deskripsi)
                                    <br>
                                    <small class="text-muted">{{ $item->deskripsi }}</small>
                                @endif
                            </td>

                            <td>
                                <span class="badge badge-{{ $kategoriColor[$item->kategori] ?? 'secondary' }}">
                                    {{ $kategoriLabel[$item->kategori] ?? ucfirst($item->kategori) }}
                                </span>
                            </td>

                            <td>
                                @if ($item->jenis == 'target')
                                    <span class="badge badge-info">Berdasarkan Target</span>
                                @else
                                    <span class="badge badge-primary">Berdasarkan Rubrik</span>
                                @endif
                            </td>

                            <td class="text-center font-weight-bold">
                                {{ number_format((float) $item->bobot, 2) }}%
                            </td>

                            <td class="text-center">
                                @if ($item->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-warning btn-edit"
                                    data-id="{{ $item->id }}" data-nama="{{ $item->nama }}"
                                    data-kategori="{{ $item->kategori }}" data-jenis="{{ $item->jenis }}"
                                    data-bobot="{{ $item->bobot }}" data-deskripsi="{{ $item->deskripsi }}"
                                    data-active="{{ $item->is_active ? 1 : 0 }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <form action="{{ route('hrd.kpi.indikator.destroy', $item->id) }}" method="POST"
                                    class="d-inline form-hapus">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada indikator KPI.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEdit" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-header bg-warning">
                        <h5 class="modal-title font-weight-bold">
                            <i class="fas fa-edit mr-1"></i> Edit Indikator KPI
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Indikator</label>
                            <input type="text" name="nama" id="editNama" class="form-control" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Kategori</label>
                                    <select name="kategori" id="editKategori" class="form-control" required>
                                        @foreach ($kategoriLabel as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Jenis Penilaian</label>
                                    <select name="jenis" id="editJenis" class="form-control" required>
                                        <option value="target">Berdasarkan Target</option>
                                        <option value="rubrik">Berdasarkan Rubrik</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Bobot (%)</label>
                                    <input type="number" name="bobot" id="editBobot" class="form-control"
                                        min="0.01" max="100" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" id="editDeskripsi" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" value="1" class="custom-control-input"
                                id="editActive">
                            <label class="custom-control-label" for="editActive">
                                Indikator Aktif
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@section('js')
    <script>
        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');

            $('#formEdit').attr(
                'action',
                "{{ url('/hrd/kpi/indikator') }}/" + id
            );

            $('#editNama').val($(this).attr('data-nama'));
            $('#editKategori').val($(this).attr('data-kategori'));
            $('#editJenis').val($(this).attr('data-jenis'));
            $('#editBobot').val($(this).attr('data-bobot'));
            $('#editDeskripsi').val($(this).attr('data-deskripsi'));
            $('#editActive').prop('checked', $(this).attr('data-active') == 1);

            $('#modalEdit').modal('show');
        });

        $(document).on('submit', '.form-hapus', function(e) {
            if (!confirm('Yakin ingin menghapus indikator KPI ini?')) {
                e.preventDefault();
            }
        });
    </script>
@stop
