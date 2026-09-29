@extends('adminlte::page')

@section('title', 'OPMC - OPM/OTDR Reader - ' . config('app.name'))

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="fas fa-file-excel text-success mr-2"></i>OPMC / OTDR Reader</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">OPMC</a></li>
                    <li class="breadcrumb-item active">Reader</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Alert Notifikasi Success / Error --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('admin.opmc.reader.process') }}" method="POST" enctype="multipart/form-data"
            id="form-opm-reader">
            @csrf

            {{-- Section 1: Data Header Project (Full Width) --}}
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Data Header Project</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="region">Region <span class="text-danger">*</span></label>
                                <input type="text" name="region" id="region"
                                    class="form-control @error('region') is-invalid @enderror" placeholder="Contoh: BEKASI"
                                    value="{{ old('region') }}" required>
                                @error('region')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="cluster">Cluster <span class="text-danger">*</span></label>
                                <input type="text" name="cluster" id="cluster"
                                    class="form-control @error('cluster') is-invalid @enderror"
                                    placeholder="Contoh: SETIA ASIH RW 23" value="{{ old('cluster') }}" required>
                                @error('cluster')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="olt_name">OLT Name</label>
                                <input type="text" name="olt_name" id="olt_name" class="form-control"
                                    placeholder="Contoh: OLT MEDAN SATRIA 03" value="{{ old('olt_name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="odf_number">ODF Number</label>
                                <input type="text" name="odf_number" id="odf_number" class="form-control"
                                    placeholder="Contoh: ODF-01" value="{{ old('odf_number') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fdt_number">FDT Number</label>
                                <input type="text" name="fdt_number" id="fdt_number" class="form-control"
                                    placeholder="Contoh: FDT 1 FDT A01" value="{{ old('fdt_number') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="opm_calibration_deviation">OPM Calibration Deviation</label>
                                <input type="text" name="opm_calibration_deviation" id="opm_calibration_deviation"
                                    class="form-control" placeholder="Contoh: 0.00"
                                    value="{{ old('opm_calibration_deviation', '0.00') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="power_meter_sn">Power Meter Model / SN</label>
                                <input type="text" name="power_meter_sn" id="power_meter_sn" class="form-control"
                                    placeholder="Contoh: EXFO-12345" value="{{ old('power_meter_sn') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" name="address" id="address" class="form-control"
                                    placeholder="Contoh: JL. RAYA SETIA ASIH" value="{{ old('address') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Form Upload File OPM (Full Width) --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-upload mr-1"></i> Form Upload File OPM</h3>
                </div>

                <div class="card-body">
                    <div class="row">
                        {{-- Input 1: File OPM SPL --}}
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="opm_spl_file">1. Upload File OPM SPL (Feeder)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('opm_spl_file') is-invalid @enderror"
                                            id="opm_spl_file" name="opm_spl_file" accept=".xlsx, .xls">
                                        <label class="custom-file-label" for="opm_spl_file">Pilih file OPM SPL...</label>
                                    </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-file-excel"></i></span>
                                    </div>
                                </div>
                                @error('opm_spl_file')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Target Sheet: <strong>E2E OPM Feeder</strong>
                                </small>
                            </div>
                        </div>

                        {{-- Input 2: File OPM Distribution --}}
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="opm_dist_file">2. Upload File OPM FAT (Distribution)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file"
                                            class="custom-file-input @error('opm_dist_file') is-invalid @enderror"
                                            id="opm_dist_file" name="opm_dist_file" accept=".xlsx, .xls">
                                        <label class="custom-file-label" for="opm_dist_file">Pilih file OPM
                                            Distribution...</label>
                                    </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i class="fas fa-file-excel"></i></span>
                                    </div>
                                </div>
                                @error('opm_dist_file')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                                <small class="form-text text-muted">
                                    Target Sheet: <strong>E2E OPM Distribution</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white text-right">
                    <button type="reset" class="btn btn-default"><i class="fas fa-undo mr-1"></i> Reset</button>
                    <button type="submit" class="btn btn-success ml-2" id="btn-submit" disabled>
                        <i class="fas fa-cogs mr-1"></i> Process & Export Template
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            // Inisialisasi bawaan AdminLTE/Bootstrap untuk file input
            if (typeof bsCustomFileInput !== 'undefined') {
                bsCustomFileInput.init();
            }

            // Backup handler jika bsCustomFileInput tidak ter-load
            $('.custom-file-input').on('change', function () {
                let fileName = $(this).val().split('\\').pop();
                if (fileName) {
                    $(this).next('.custom-file-label').addClass("selected").html(fileName);
                }
            });

            // Loading state saat submit
            $('#form-opm-reader').on('submit', function () {
                let btn = $('#btn-submit');
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Memproses & Mengisi Template...');

                // Re-enable tombol setelah interval (jika unduhan file dimulai)
                setTimeout(function () {
                    btn.prop('disabled', false);
                    btn.html('<i class="fas fa-cogs mr-1"></i> Process & Export Template');
                }, 5000);
            });
        });
    </script>
@endsection