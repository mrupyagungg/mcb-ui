@extends('adminlte::page')

@section('title', 'Pengelolaan Assets')

@section('content_header')
<h1 class="m-0 text-dark">
    <i class="fas fa-boxes mr-2"></i> Pengelolaan Assets
</h1>
@stop

@section('content')
<div class="container-fluid">

    {{-- INFORMASI ASSETS --}}
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>0</h3>
                    <p>Total Assets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-boxes"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>0</h3>
                    <p>Assets Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>0</h3>
                    <p>Dalam Perbaikan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>0</h3>
                    <p>Assets Rusak</p>
                </div>
                <div class="icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL ASSETS --}}
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="fas fa-list mr-1"></i> Data Inventaris Assets
            </h3>

            <div class="card-tools">
                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalTambahAsset">
                    <i class="fas fa-plus mr-1"></i> Tambah Asset
                </button>

                <button class="btn btn-info btn-sm">
                    <i class="fas fa-file-import mr-1"></i> Import Excel
                </button>

                <button class="btn btn-secondary btn-sm">
                    <i class="fas fa-file-export mr-1"></i> Export Excel
                </button>
            </div>
        </div>

        <div class="card-body">

            {{-- FILTER --}}
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Cari kode, nama asset, serial number...">
                </div>

                <div class="col-md-3">
                    <select class="form-control">
                        <option value="">Semua Kategori</option>
                        <option>Elektronik</option>
                        <option>Peralatan Kerja</option>
                        <option>Kendaraan</option>
                        <option>Furniture</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-control">
                        <option value="">Semua Kondisi</option>
                        <option>Baik</option>
                        <option>Dalam Perbaikan</option>
                        <option>Rusak</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="bg-light">
                        <tr class="text-center">
                            <th width="50">No</th>
                            <th>Kode Asset</th>
                            <th>Nama Asset</th>
                            <th>Kategori</th>
                            <th>Serial Number</th>
                            <th>Lokasi</th>
                            <th>Pemegang Asset</th>
                            <th>Kondisi</th>
                            <th width="130">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <br>
                                Belum ada data assets.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- MODAL TAMBAH ASSET --}}
<div class="modal fade" id="modalTambahAsset" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white">
                    <i class="fas fa-plus-circle mr-1"></i> Tambah Asset Baru
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <form>
                @csrf

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kode Asset</label>
                                <input type="text" class="form-control" placeholder="Contoh: AST-001">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Asset</label>
                                <input type="text" class="form-control" placeholder="Nama barang / asset">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    <option>Elektronik</option>
                                    <option>Peralatan Kerja</option>
                                    <option>Kendaraan</option>
                                    <option>Furniture</option>
                                    <option>Lainnya</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Serial Number</label>
                                <input type="text" class="form-control" placeholder="Serial number asset">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Pembelian</label>
                                <input type="date" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Harga Pembelian</label>
                                <input type="number" class="form-control" placeholder="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lokasi Asset</label>
                                <input type="text" class="form-control" placeholder="Contoh: Kantor Pusat">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pemegang Asset</label>
                                <input type="text" class="form-control" placeholder="Nama karyawan">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kondisi Asset</label>
                                <select class="form-control">
                                    <option value="baik">Baik</option>
                                    <option value="perbaikan">Dalam Perbaikan</option>
                                    <option value="rusak">Rusak</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status Asset</label>
                                <select class="form-control">
                                    <option value="aktif">Aktif</option>
                                    <option value="nonaktif">Nonaktif</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea class="form-control" rows="3" placeholder="Catatan tambahan asset"></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Asset
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@stop