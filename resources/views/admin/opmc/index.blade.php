@extends('adminlte::page')

@section('title', 'OPMC - OCR Foto OPM ke Teks - ' . config('app.name'))

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-camera mr-2"></i>OPMC OCR Reader (Lokal)</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">OPMC</a></li>
                    <li class="breadcrumb-item active">OCR Reader</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Alert Notifikasi --}}
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row">
            {{-- KOLOM KIRI: UPLOAD & PREVIEW FOTO --}}
            <div class="col-lg-5">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-upload mr-1"></i> 1. Upload Foto OPM / Form FDT
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="ocrForm" action="{{ route('admin.opmc.process-ocr') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="photo" class="font-weight-bold">Pilih Foto Lapangan (.jpg, .jpeg, .png)</label>
                                <div class="custom-file">
                                    <input type="file" name="photo" class="custom-file-input" id="photo" accept="image/*"
                                        required>
                                    <label class="custom-file-label" for="photo">Pilih atau letakkan foto...</label>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle mr-1"></i> Pastikan angka di layar OPM dan tulisan cetak
                                    terlihat jelas & tidak silau.
                                </small>
                            </div>

                            {{-- Preview Gambar --}}
                            <div class="text-center mb-3 d-none" id="imagePreviewContainer">
                                <div class="border rounded p-2 bg-light">
                                    <img id="imagePreview" src="#" alt="Preview Foto" class="img-fluid rounded"
                                        style="max-height: 350px;">
                                </div>
                            </div>

                            <button type="button" id="btnProcessOcr"
                                class="btn btn-primary btn-block font-weight-bold disabled">
                                <i class="fas fa-microchip mr-1"></i> Ekstrak Data dengan OCR Lokal
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PREVIEW & EDIT HASIL OCR --}}
            <div class="col-lg-7">
                <div class="card card-success card-outline shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold">
                            <i class="fas fa-edit mr-1"></i> 2. Review & Edit Hasil Ekstraksi Teks
                        </h3>
                        <span class="badge badge-secondary" id="ocrStatusBadge">Belum diproses</span>
                    </div>

                    <div class="card-body">
                        {{-- Loading Overlay / Spinner --}}
                        <div id="loadingOverlay" class="text-center py-5 d-none">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                <span class="sr-only">Membaca data gambar...</span>
                            </div>
                            <p class="mt-3 font-weight-bold text-muted">Sedang menganalisis gambar via OCR Lokal
                                (PaddleOCR)...</p>
                        </div>

                        {{-- Form Edit Data Hasil OCR --}}
                        <form id="exportForm" action="{{ route('admin.opmc.generate-excel') }}" method="POST">
                            @csrf
                            <div id="ocrResultContainer" class="d-none">
                                <h6 class="font-weight-bold text-primary border-bottom pb-2">
                                    <i class="fas fa-info-circle mr-1"></i> Data Informasi Header
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold mb-1">Tanggal & Waktu</label>
                                        <input type="text" name="tanggal" id="res_tanggal"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold mb-1">Lokasi</label>
                                        <input type="text" name="lokasi" id="res_lokasi"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold mb-1">Cluster</label>
                                        <input type="text" name="cluster" id="res_cluster"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-6 form-group mb-2">
                                        <label class="small font-weight-bold mb-1">FDT ID</label>
                                        <input type="text" name="fdt_id" id="res_fdt_id"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="small font-weight-bold mb-1">SPL NO</label>
                                        <input type="text" name="spl_no" id="res_spl_no"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="small font-weight-bold mb-1">Spliter Ratio</label>
                                        <input type="text" name="spliter_ratio" id="res_spliter_ratio"
                                            class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-4 form-group mb-2">
                                        <label class="small font-weight-bold mb-1">Input (dBm)</label>
                                        <input type="text" name="input_dbm" id="res_input_dbm"
                                            class="form-control form-control-sm">
                                    </div>
                                </div>

                                <h6 class="font-weight-bold text-primary border-bottom pb-2 mt-3">
                                    <i class="fas fa-list-ol mr-1"></i> Data Nilai Output Spliter (dBm)
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm text-center">
                                        <thead class="thead-light">
                                            <tr>
                                                <th style="width: 20%;">Port Output</th>
                                                <th style="width: 40%;">Nilai Ukur (dBm)</th>
                                                <th style="width: 40%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="portTableBody">
                                            {{-- Baris Port 1 s/d 8 di-generate secara dinamis via JS --}}
                                        </tbody>
                                    </table>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success btn-block font-weight-bold">
                                        <i class="fas fa-file-excel mr-1"></i> Generate ke Template Excel
                                    </button>
                                </div>
                            </div>

                            {{-- Pesan Kosong Sebelum OCR Diproses --}}
                            <div id="emptyState" class="text-center py-5 text-muted">
                                <i class="fas fa-arrow-left fa-2x mb-2"></i>
                                <p>Silakan upload foto OPM/FDT di sebelah kiri lalu klik tombol <b>Ekstrak Data</b>.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            // Preview foto setelah dipilih
            $('#photo').on('change', function () {
                let fileName = $(this).val().split('\\').pop(); $(this).next('.custom-file-label').addClass("selected").html(fileName);

                let reader = new FileReader();
                reader.onload = function (e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreviewContainer').removeClass('d-none');
                }
                if (this.files[0]) {
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // Eksekusi AJAX OCR
            $('#btnProcessOcr').on('click', function () {
                let fileInput = $('#photo')[0].files[0];
                if (!fileInput) {
                    alert('Silakan pilih foto terlebih dahulu!');
                    return;
                }

                let formData = new FormData();
                formData.append('photo', fileInput);
                formData.append('_token', '{{ csrf_token() }}');

                // UI Loading state
                $('#emptyState').addClass('d-none');
                $('#ocrResultContainer').addClass('d-none');
                $('#loadingOverlay').removeClass('d-none');
                $('#ocrStatusBadge').removeClass('badge-secondary badge-success badge-danger').addClass('badge-warning').text('Memproses...');

                $.ajax({
                    url: "{{ route('admin.opmc.process-ocr') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#loadingOverlay').addClass('d-none');

                        if (response.success) {
                            let data = response.data;

                            // Default tanggal jika OCR tidak menemukan teks tanggal
                            let defaultDate = new Date().toISOString().slice(0, 10);

                            // Fill Header Info
                            $('#res_tanggal').val(data.tanggal || defaultDate);
                            $('#res_lokasi').val(data.lokasi || '');
                            $('#res_cluster').val(data.cluster || '');
                            $('#res_fdt_id').val(data.fdt_id || '');
                            $('#res_spl_no').val(data.spl_no || '');
                            $('#res_spliter_ratio').val(data.spliter_ratio || '');
                            $('#res_input_dbm').val(data.input_dbm || '');

                            // Generate Port Table Rows (1 - 8)
                            let tableHtml = '';
                            let ports = data.output_ports || [];

                            for (let i = 1; i <= 8; i++) {
                                let portData = ports.find(p => p.port == i);
                                let val = portData && portData.value_dbm !== null ? portData.value_dbm : '';
                                let status = val !== '' ? '<span class="badge badge-success">OK</span>' : '<span class="badge badge-secondary">-</span>';

                                tableHtml += `
                                                    <tr>
                                                        <td class="align-middle font-weight-bold">Port ${i}</td>
                                                        <td>
                                                            <input type="text" name="ports[${i}]" value="${val}" class="form-control form-control-sm text-center">
                                                        </td>
                                                        <td class="align-middle">${status}</td>
                                                    </tr>
                                                `;
                            }
                            $('#portTableBody').html(tableHtml);

                            $('#ocrResultContainer').removeClass('d-none');
                            $('#ocrStatusBadge').removeClass('badge-warning').addClass('badge-success').text('Selesai');
                        } else {
                            alert('Gagal membaca data dari gambar: ' + (response.message || 'Error tidak diketahui'));
                            $('#ocrStatusBadge').removeClass('badge-warning').addClass('badge-danger').text('Gagal');
                            $('#emptyState').removeClass('d-none');
                        }
                    },
                    error: function (xhr) {
                        $('#loadingOverlay').addClass('d-none');
                        $('#emptyState').removeClass('d-none');
                        $('#ocrStatusBadge').removeClass('badge-warning').addClass('badge-danger').text('Error');

                        let errMessage = 'Terjadi kesalahan pada server OCR lokal.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errMessage += '\nDetail: ' + xhr.responseJSON.message;
                        }
                        alert(errMessage);
                    }
                });
            });
        });
    </script>
@endsection