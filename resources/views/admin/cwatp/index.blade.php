@extends('adminlte::page')

@section('title', 'Form Input ATP Document - ' . config('app.name'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center mb-2">
    <h1 class="m-0 text-dark font-weight-bold">
        ATP Document Generator
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item active">Generate ATP</li>
        </ol>
    </nav>
</div>
@stop

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <!-- Kartu Utama Full Width (12 Kolom Penuh) -->
        <div class="col-12">
            <div class="card card-outline card-success shadow-sm rounded-lg border-0">

                <!-- Card Header -->
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-success text-white rounded-circle p-2 mr-3 d-flex align-items-center justify-content-center"
                            style="width: 42px; height: 42px;">
                            <i class="fas fa-cloud-upload-alt fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="card-title font-weight-bold m-0 text-dark">Form Import Bulk ATP</h5><br>
                            <small class="text-muted d-block">Pilih jenis template ATP yang ingin di-generate</small>
                        </div>
                    </div>
                </div>

                <!-- Nav Tabs Pilihan Jenis ATP -->
                <div class="card-header bg-light p-2 border-top border-bottom">
                    <ul class="nav nav-pills nav-fill" id="atpTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active font-weight-bold py-2" id="distribusi-tab" data-toggle="tab"
                                href="#distribusi" role="tab" aria-controls="distribusi" aria-selected="true">
                                <i class="fas fa-network-wired mr-1"></i> ATP Distribusi
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link font-weight-bold py-2" id="subfeeder-tab" data-toggle="tab"
                                href="#subfeeder" role="tab" aria-controls="subfeeder" aria-selected="false">
                                <i class="fas fa-project-diagram mr-1"></i> ATP Subfeeder
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body pt-3">
                    {{-- Alert Error Session --}}
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-lg"
                            role="alert">
                            <div class="d-flex align-items-center">
                                <i class="icon fas fa-exclamation-triangle fa-lg mr-3"></i>
                                <div>
                                    <strong>Terjadi Kesalahan!</strong>
                                    <div class="small">{{ session('error') }}</div>
                                </div>
                            </div>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="tab-content" id="atpTabContent">

                        <!-- ========================================== -->
                        <!-- FORM 1: ATP DISTRIBUSI                     -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade show active" id="distribusi" role="tabpanel"
                            aria-labelledby="distribusi-tab">

                            <div class="mb-3">
                                <small class="font-weight-bold text-muted d-block mb-1">Ketentuan Kata Kunci Foto
                                    (Distribusi):</small>
                                <div
                                    class="badge badge-warning text-dark font-weight-bold p-2 text-wrap text-left leading-relaxed">
                                    <span class="mr-3 mb-1 d-inline-block"><code>cls</code> = FAT Close</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>opn</code> = FAT Open</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>acc</code> = Accessories</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>idpole</code> = ID Pole</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>pondasi</code> = Pondasi Pole</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>pole</code> = View Pole</span>
                                </div>
                            </div>

                            <form action="{{ route('admin.cwatp.export-bulk') }}" method="POST"
                                enctype="multipart/form-data" class="atp-form">
                                @csrf
                                <input type="hidden" name="type" value="distribusi">

                                <div class="bg-light p-3 rounded-lg mb-4 border">
                                    <h6 class="text-uppercase text-secondary font-weight-bold mb-3 small"
                                        style="letter-spacing: 0.5px;">
                                        <i class="fas fa-info-circle mr-1"></i> Informasi Header Excel (Distribusi)
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-normal text-dark small mb-1">Region <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="region" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-normal text-dark small mb-1">OLT Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="olt_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="font-weight-normal text-dark small mb-1">Cluster Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="cluster_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="font-weight-normal text-dark small mb-1">Cluster ID <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="cluster_id" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label
                                        class="font-weight-bold text-dark d-flex align-items-center justify-content-between">
                                        <span><i class="fas fa-file-archive text-warning mr-1"></i> Upload ZIP Foto
                                            Distribusi</span>
                                        <span class="badge badge-warning text-dark font-weight-bold">Maks. 20 FAT</span>
                                    </label>

                                    <div class="custom-file mb-2">
                                        <input type="file" name="zip_file" class="custom-file-input zip-input"
                                            id="zipDistribusi" accept=".zip" required>
                                        <label class="custom-file-label text-muted" for="zipDistribusi">Pilih file .ZIP
                                            dari komputer...</label>
                                    </div>

                                    <div class="p-3 bg-white border rounded-lg small text-secondary">
                                        <div class="font-weight-bold text-dark mb-1"><i
                                                class="fas fa-lightbulb text-warning mr-1"></i> Ketentuan ZIP
                                            Distribusi:
                                        </div>
                                        <ul class="pl-3 mb-0">
                                            <li>Folder di dalam ZIP dinamai per FAT (contoh:
                                                <code>IMPLEMENTASI-DS</code>,
                                                <code>FOTO-FDT</code>).
                                            </li>
                                            <li>Setiap folder berisi foto sesuai kata kunci (FAT Close, Open,
                                                Accessories, ID Pole, Pondasi, Pole).</li>
                                            <li>Maksimal <strong>20 folder FAT</strong>.</li>
                                        </ul>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="btn btn-success btn-lg btn-block shadow-sm font-weight-bold py-2">
                                    <i class="fas fa-file-download mr-2"></i> Export ATP Distribusi
                                </button>
                            </form>
                        </div>

                        <!-- ========================================== -->
                        <!-- FORM 2: ATP SUBFEEDER                      -->
                        <!-- ========================================== -->
                        <div class="tab-pane fade" id="subfeeder" role="tabpanel" aria-labelledby="subfeeder-tab">

                            <div class="mb-3">
                                <small class="font-weight-bold text-muted d-block mb-1">Ketentuan Kata Kunci Foto
                                    (Subfeeder):</small>
                                <div
                                    class="badge badge-info text-white font-weight-bold p-2 text-wrap text-left leading-relaxed">
                                    <span class="mr-3 mb-1 d-inline-block"><code>cls</code> = FDT
                                        Close</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>opn</code> = FDT
                                        Open</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>acc</code> = FDT Accessories</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>idpole</code> = FDT Id Pole</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>pondasi</code> = FDT Pondasi
                                        Pole</span>
                                    <span class="mr-3 mb-1 d-inline-block"><code>jc</code> = Install JC</span>
                                </div>
                            </div>

                            <form action="{{ route('admin.cwatp.export-bulk') }}" method="POST"
                                enctype="multipart/form-data" class="atp-form">
                                @csrf
                                <input type="hidden" name="type" value="subfeeder">

                                <div class="bg-light p-3 rounded-lg mb-4 border">
                                    <h6 class="text-uppercase text-secondary font-weight-bold mb-3 small"
                                        style="letter-spacing: 0.5px;">
                                        <i class="fas fa-info-circle mr-1"></i> Informasi Header Excel (Subfeeder)
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-normal text-dark small mb-1">Region <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="region" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-normal text-dark small mb-1">OLT Name <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="olt_name" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="font-weight-normal text-dark small mb-1">Feeder / Segment Name
                                                <span class="text-danger">*</span></label>
                                            <input type="text" name="cluster_name" class="form-control"
                                                placeholder="Contoh: FDR_01" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="font-weight-normal text-dark small mb-1">Feeder ID <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="cluster_id" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-4">
                                    <label
                                        class="font-weight-bold text-dark d-flex align-items-center justify-content-between">
                                        <span><i class="fas fa-file-archive text-info mr-1"></i> Upload ZIP Foto
                                            Subfeeder</span>
                                        <span class="badge badge-info font-weight-bold">Maks. 20 Section</span>
                                    </label>

                                    <div class="custom-file mb-2">
                                        <input type="file" name="zip_file" class="custom-file-input zip-input"
                                            id="zipSubfeeder" accept=".zip" required>
                                        <label class="custom-file-label text-muted" for="zipSubfeeder">Pilih file .ZIP
                                            dari komputer...</label>
                                    </div>

                                    <div class="p-3 bg-white border rounded-lg small text-secondary">
                                        <div class="font-weight-bold text-dark mb-1"><i
                                                class="fas fa-lightbulb text-info mr-1"></i> Ketentuan ZIP Subfeeder:
                                        </div>
                                        <ul class="pl-3 mb-0">
                                            <li>Folder di dalam ZIP dinamai per Section/Closure (contoh:
                                                <code>Impementasi-SF</code>, <code>Foto_FDT</code>).
                                            </li>
                                            <li>Setiap folder berisi foto sesuai kata kunci Subfeeder (FJC/Closure,
                                                OTDR, OPM, ID Pole, Route).</li>
                                            <li>Maksimal <strong>20 folder Subfeeder</strong>.</li>
                                        </ul>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="btn btn-info btn-lg btn-block shadow-sm font-weight-bold py-2">
                                    <i class="fas fa-file-download mr-2"></i> Export ATP Subfeeder
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Mengubah label custom file input saat ada file yang dipilih
        document.querySelectorAll('.custom-file-input').forEach(function (input) {
            input.addEventListener('change', function (e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file .ZIP dari komputer...';
                var nextSibling = e.target.nextElementSibling;
                if (nextSibling) {
                    nextSibling.innerText = fileName;
                }
            });
        });

        // 2. Intercept Submit Form untuk AJAX Download + SweetAlert
        document.querySelectorAll('.atp-form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(form);

                // Indikator Loading / Proses
                Swal.fire({
                    title: 'Memproses Dokumen ATP...',
                    text: 'Mohon tunggu, sistem sedang membuat file Excel',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Request via Fetch
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(async response => {
                        if (!response.ok) {
                            const errorData = await response.json().catch(() => null);
                            throw new Error(errorData && errorData.message ? errorData.message : 'Gagal memproses file. Pastikan format input dan file ZIP sudah benar.');
                        }

                        // Ambil nama file dari header Content-Disposition (jika ada dari Controller)
                        let filename = "ATP_Document_Result_" + new Date().getTime() + ".xlsx";
                        const disposition = response.headers.get('Content-Disposition');
                        if (disposition && disposition.indexOf('filename=') !== -1) {
                            const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                            if (matches != null && matches[1]) {
                                filename = matches[1].replace(/['"]/g, '');
                            }
                        }

                        const blob = await response.blob();
                        return { blob, filename };
                    })
                    .then(({ blob, filename }) => {
                        // Trigger Download
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        a.remove();
                        window.URL.revokeObjectURL(url);

                        // Alert Berhasil
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Dokumen ATP telah selesai diproses dan berhasil diunduh.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        });
                    })
                    .catch(error => {
                        // Alert Gagal
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memproses',
                            text: error.message || 'Terjadi kesalahan saat memproses data.',
                            confirmButtonColor: '#dc3545'
                        });
                    });
            });
        });
    });
</script>
@stop