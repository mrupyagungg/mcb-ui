
@extends('adminlte::page')

@section('title', 'Penilaian KPI')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="font-weight-bold mb-0">
            <i class="fas fa-clipboard-check mr-2 text-primary"></i>
            Penilaian KPI
        </h1>
        <small class="text-muted">Evaluasi kinerja dan pencapaian karyawan MCB</small>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali inputan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('hrd.kpi.penilaian.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        {{-- INFORMASI PENILAIAN --}}
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-user mr-2"></i>
                    Informasi Penilaian
                </h3>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Karyawan <span class="text-danger">*</span></label>
                            <select name="user_id" class="form-control select2" required>
                                <option value="">Pilih Karyawan</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ old('user_id') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Periode Penilaian <span class="text-danger">*</span></label>
                            <select name="period_id" class="form-control" required>
                                <option value="">Pilih Periode</option>
                                @foreach($periods as $period)
                                    <option value="{{ $period->id }}">
                                        {{ $period->nama_periode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Kategori Pekerjaan <span class="text-danger">*</span></label>
                            <select name="kategori" id="kategori"
                                    class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                <option value="dokumen">Document Controller</option>
                                <option value="lapangan">Field / Lapangan</option>
                                <option value="drafter">Drafter</option>
                                <option value="koordinator">Koordinator</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- INDIKATOR KPI --}}
        <div class="card card-outline card-info shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-list-check mr-2"></i>
                    Indikator Penilaian
                </h3>
            </div>

            <div class="card-body">

                <div id="indikator-placeholder" class="text-center py-4">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">
                        Silakan pilih kategori pekerjaan terlebih dahulu.
                    </p>
                </div>

                @foreach($indikators as $indikator)
                    <div class="indikator-item mb-4 p-3 border rounded"
                         data-kategori="{{ $indikator->kategori }}"
                         style="display:none;">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold mb-1">
                                    {{ $indikator->nama }}
                                </h6>
                                <small class="text-muted">
                                    {{ $indikator->deskripsi }}
                                </small>
                            </div>

                            <div class="text-right">
                                <span class="badge badge-primary">
                                    Bobot {{ $indikator->bobot }}%
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ ucfirst($indikator->jenis) }}
                                </small>
                            </div>
                        </div>

                        <input type="hidden"
                               name="details[{{ $indikator->id }}][indicator_id]"
                               value="{{ $indikator->id }}">

                        @if($indikator->jenis == 'target')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Target</label>
                                        <input type="number"
                                               name="details[{{ $indikator->id }}][target]"
                                               class="form-control"
                                               min="0.01"
                                               step="0.01"
                                               placeholder="Masukkan target">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Realisasi</label>
                                        <input type="number"
                                               name="details[{{ $indikator->id }}][realisasi]"
                                               class="form-control"
                                               min="0"
                                               step="0.01"
                                               placeholder="Masukkan realisasi">
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="form-group">
                                <label>Nilai Rubrik (1–5)</label>
                                <select name="details[{{ $indikator->id }}][nilai_rubrik]"
                                        class="form-control">
                                    <option value="">Pilih Nilai</option>
                                    <option value="1">1 - Sangat Kurang</option>
                                    <option value="2">2 - Kurang</option>
                                    <option value="3">3 - Cukup</option>
                                    <option value="4">4 - Baik</option>
                                    <option value="5">5 - Sangat Baik</option>
                                </select>
                            </div>
                        @endif

                        <div class="form-group">
                            <label>Bukti / Keterangan Penilaian</label>
                            <textarea
                                name="details[{{ $indikator->id }}][bukti]"
                                class="form-control"
                                rows="2"
                                placeholder="Jelaskan pencapaian atau bukti penilaian..."></textarea>
                        </div>

                        <div class="form-group mb-0">
                            <label>Lampiran Bukti (Opsional)</label>
                            <input type="file"
                                   name="details[{{ $indikator->id }}][file]"
                                   class="form-control-file"
                                   accept=".jpg,.jpeg,.png,.pdf,.xlsx,.xls">
                        </div>

                    </div>
                @endforeach

            </div>
        </div>

        {{-- CATATAN KOORDINATOR --}}
        <div class="card card-outline card-secondary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-comment-alt mr-2"></i>
                    Evaluasi Keseluruhan
                </h3>
            </div>

            <div class="card-body">
                <div class="form-group mb-0">
                    <label>Catatan Koordinator</label>
                    <textarea name="catatan"
                              class="form-control"
                              rows="4"
                              placeholder="Tuliskan evaluasi keseluruhan karyawan..."></textarea>
                </div>
            </div>
        </div>

        {{-- TOMBOL --}}
        <div class="card shadow-sm">
            <div class="card-body d-flex justify-content-end">

                <a href="{{ route('hrd.kpi.dashboard') }}"
                   class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Kembali
                </a>

                <button type="submit" name="status" value="draft"
                        class="btn btn-outline-primary mr-2">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Draft
                </button>

                <button type="submit" name="status" value="submitted"
                        class="btn btn-primary">
                    <i class="fas fa-paper-plane mr-1"></i>
                    Ajukan ke HRD
                </button>

            </div>
        </div>

    </form>
</div>
@stop

@section('js')
<script>
$(function () {
    $('#kategori').on('change', function () {
        let kategori = $(this).val();

        $('.indikator-item').hide();

        if (!kategori) {
            $('#indikator-placeholder').show();
            return;
        }

        $('#indikator-placeholder').hide();

        $('.indikator-item').each(function () {
            if ($(this).data('kategori') === kategori) {
                $(this).show();
            }
        });
    });

    $('#kategori').trigger('change');
});
</script>
@stop
