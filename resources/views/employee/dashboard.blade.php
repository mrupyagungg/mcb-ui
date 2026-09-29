@extends('adminlte::page')

@section('title', 'DASHBOARD - ' . config('app.name'))

@section('content_header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark font-weight-bold">
                Dashboard System
            </h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
</div>
@stop

@section('content')
<div class="container-fluid">

    <!-- 1. Row Info Boxes / Stat Cards -->
    <div class="row">
        <!-- Card Total Document -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0">
                <span class="info-box-icon bg-info elevation-1 rounded-circle"><i class="fas fa-file-excel"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">Total ATP Generated</span>
                    <span class="info-box-number text-dark h4 mb-0 font-weight-bold">1,284</span>
                </div>
            </div>
        </div>

        <!-- Card ATP Distribusi -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0">
                <span class="info-box-icon bg-success elevation-1 rounded-circle"><i
                        class="fas fa-network-wired"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">ATP Distribusi (DS)</span>
                    <span class="info-box-number text-dark h4 mb-0 font-weight-bold">842</span>
                </div>
            </div>
        </div>

        <!-- Card ATP Subfeeder -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0">
                <span class="info-box-icon bg-primary elevation-1 rounded-circle"><i
                        class="fas fa-project-diagram"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-muted">ATP Subfeeder (SF)</span>
                    <span class="info-box-number text-dark h4 mb-0 font-weight-bold">442</span>
                </div>
            </div>
        </div>

        <!-- Card Quick Action Link -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm rounded-lg border-0 bg-success text-white">
                <div class="info-box-content">
                    <span class="info-box-text">Aksi Cepat</span>
                    <a href="{{ route('admin.cwatp') }}"
                        class="btn btn-light btn-sm font-weight-bold mt-1 text-success shadow-xs">
                        <i class="fas fa-plus-circle mr-1"></i> Form Bulk ATP
                    </a>
                </div>
                <span class="info-box-icon"><i class="fas fa-cloud-upload-alt"></i></span>
            </div>
        </div>
    </div>

    <!-- 2. Row Charts & Quick Actions -->
    <div class="row">

        <!-- Quick Info Panel -->
        <div class="col-lg-4">
            <div class="card card-outline card-info shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h3 class="card-title font-weight-bold text-dark m-0">
                        <i class="fas fa-info-circle text-info mr-2"></i>Panduan Sistem Generate CW ATP
                    </h3>
                </div>
                <div class="card-body">
                    <div class="callout callout-success mb-3 bg-light">
                        <h6 class="font-weight-bold text-success"><i class="fas fa-folder mr-1"></i> Format ZIP
                            Distribusi</h6>
                        <small class="text-secondary d-block">
                            Pastikan ZIP memiliki folder <code>Impelemntasi</code>, <code>FDT</code>, dan folder FAT
                            seperti <code>FAT_01</code>, <code>FAT_02</code>.
                        </small>
                    </div>

                    <div class="callout callout-info mb-3 bg-light">
                        <h6 class="font-weight-bold text-info"><i class="fas fa-folder mr-1"></i> Format ZIP Subfeeder
                        </h6>
                        <small class="text-secondary d-block">
                            Pastikan ZIP memiliki folder <code>IMPLEMENTASI DS</code> dan <code>FDT</code>.
                        </small>
                    </div>

                    <div class="callout callout-warning mb-0 bg-light">
                        <h6 class="font-weight-bold text-warning"><i class="fas fa-exclamation-triangle mr-1"></i> Batas
                            Maksimum</h6>
                        <small class="text-secondary d-block">
                            Maksimal <strong>20 folder FAT/Section</strong> per sekali proses ekspor file Excel.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar User Terdaftar (Baru Ditambahkan) -->
        <div class="col-lg-8">
            <div class="card card-outline card-primary shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-dark m-0">
                            <i class="fas fa-users text-primary mr-2"></i>Daftar User Terdaftar
                        </h3>
                        <a href="#" class="btn btn-sm btn-outline-primary">Kelola User</a>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped mb-0 text-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. Row Riwayat Aktivitas Ekspor Terakhir -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-secondary shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title font-weight-bold text-dark m-0">
                            <i class="fas fa-history text-secondary mr-2"></i>Riwayat Generate ATP Terakhir
                        </h3>
                        <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua Log</a>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover table-striped mb-0 text-nowrap">
                        <thead class="thead-light">
                            <tr>
                                <th>Waktu Ekspor</th>
                                <th>Tipe ATP</th>
                                <th>Region</th>
                                <th>OLT Name</th>
                                <th>Cluster / Feeder</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>28 Sep 2026 18:55</td>
                                <td><span class="badge badge-success">Distribusi</span></td>
                                <td>BEKASI</td>
                                <td>OLT CIKARANG UTARA</td>
                                <td>CIKARANG KOTA RW 08 BEKASI</td>
                                <td><span class="badge badge-subtle badge-success text-success font-weight-bold"><i
                                            class="fas fa-check-circle mr-1"></i> Selesai</span></td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-outline-success"><i class="fas fa-download mr-1"></i>
                                        Re-Download</button>
                                </td>
                            </tr>
                            <tr>
                                <td>28 Sep 2026 17:30</td>
                                <td><span class="badge badge-info">Subfeeder</span></td>
                                <td>BEKASI</td>
                                <td>OLT CIKARANG UTARA</td>
                                <td>FDR_CIKARANG_01</td>
                                <td><span class="badge badge-subtle badge-success text-success font-weight-bold"><i
                                            class="fas fa-check-circle mr-1"></i> Selesai</span></td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-outline-success"><i class="fas fa-download mr-1"></i>
                                        Re-Download</button>
                                </td>
                            </tr>
                            <tr>
                                <td>28 Sep 2026 15:10</td>
                                <td><span class="badge badge-success">Distribusi</span></td>
                                <td>BOGOR</td>
                                <td>OLT CIBINONG</td>
                                <td>CIBINONG ASRI RW 02</td>
                                <td><span class="badge badge-subtle badge-success text-success font-weight-bold"><i
                                            class="fas fa-check-circle mr-1"></i> Selesai</span></td>
                                <td class="text-center">
                                    <button class="btn btn-xs btn-outline-success"><i class="fas fa-download mr-1"></i>
                                        Re-Download</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('css')
<style>
    .info-box {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .info-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
    }

    .badge-subtle {
        background-color: rgba(40, 167, 69, 0.1);
        padding: 5px 10px;
        border-radius: 4px;
    }
</style>
@stop

@section('js')
<!-- Perbaikan sintaks selector jQuery untuk Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function () {
        // Jika Anda ingin mengaktifkan kembali chart-nya, pastikan elemen <canvas id="atpExportChart"></canvas> sudah ada di HTML.

        // var ctx = document.getElementById('atpExportChart').getContext('2d');
        // var atpChart = new Chart(ctx, {
        //     type: 'bar',
        //     data: {
        //         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep'],
        //         datasets: [
        //             {
        //                 label: 'ATP Distribusi',
        //                 backgroundColor: '#28a745',
        //                 borderColor: '#28a745',
        //                 data: [65, 59, 80, 81, 56, 55, 90, 110, 120]
        //             },
        //             {
        //                 label: 'ATP Subfeeder',
        //                 backgroundColor: '#17a2b8',
        //                 borderColor: '#17a2b8',
        //                 data: [28, 48, 40, 19, 86, 27, 60, 75, 88]
        //             }
        //         ]
        //     },
        //     options: {
        //         responsive: true,
        //         maintainAspectRatio: false,
        //         scales: {
        //             y: {
        //                 beginAtZero: true
        //             }
        //         }
        //     }
        // });

    });
</script>
@stop