@extends('adminlte::page')

@section('title', 'Assets Management')

@section('content_header')
<h1>Assets Management</h1>
@stop

@section('content')

<div class="container-fluid">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @include('hrd.partials.assets-import')

    {{-- STATISTICS --}}
    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalAssets }}</h3>
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
                    <h3>{{ $availableAssets }}</h3>
                    <p>Available</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $assignedAssets }}</h3>
                    <p>Assigned Assets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-tag"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $maintenanceAssets }}</h3>
                    <p>Maintenance</p>
                </div>
                <div class="icon">
                    <i class="fas fa-tools"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- ASSET TABLE --}}

    <div class="card card-outline card-primary">

        {{-- HEADER --}}
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-laptop mr-1"></i>
                Asset List
            </h3>

            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalAddAsset">
                    <i class="fas fa-plus"></i> Tambah Asset
                </button>
            </div>
        </div>

        {{-- BODY --}}
        <div class="card-body">

            {{-- FILTER --}}
            <form action="{{ route('hrd.assets') }}" method="GET">
                <div class="row">

                    <div class="col-md-3 mb-2">
                        <input type="text" name="search" class="form-control" placeholder="Cari asset..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="category" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach(['Available', 'Active', 'Maintenance', 'Lost', 'Retired'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <select name="condition" class="form-control">
                            <option value="">Semua Kondisi</option>
                            @foreach(['Good', 'Fair', 'Damaged', 'Unrepairable'] as $condition)
                                <option value="{{ $condition }}" {{ request('condition') == $condition ? 'selected' : '' }}>
                                    {{ $condition }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-info btn-block">
                            <i class="fas fa-search"></i> Filter
                        </button>
                    </div>

                </div>
            </form>

            {{-- TABEL ASSET --}}
            <div class="table-responsive mt-3">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="bg-dark">
                        <tr>
                            <th width="45">No</th>
                            <th>Asset Code</th>
                            <th>Asset</th>
                            <th>Serial Number</th>
                            <th>PIC</th>
                            <th>Area</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Condition</th>
                            <th width="120" class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($assets as $asset)
                            <tr>
                                <td>{{ $assets->firstItem() + $loop->index }}</td>

                                <td>
                                    <strong>{{ $asset->asset_code }}</strong>
                                </td>

                                <td>
                                    <strong>{{ $asset->category }}</strong><br>
                                    <small class="text-muted">
                                        {{ $asset->brand }} {{ $asset->model }}
                                    </small>
                                </td>

                                <td>{{ $asset->serial_number ?? '-' }}</td>

                                <td>
                                    @if($asset->pic)
                                        <span>
                                            <i class="fas fa-user-circle text-primary mr-1"></i>
                                            {{ $asset->pic->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-user-slash mr-1"></i>
                                            Unassigned
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $asset->area ?? '-' }}</td>

                                <td>{{ $asset->location ?? '-' }}</td>

                                {{-- STATUS --}}
                                <td>
                                    @php
                                        $statusColor = [
                                            'Available' => 'success',
                                            'Active' => 'primary',
                                            'Maintenance' => 'warning',
                                            'Lost' => 'danger',
                                            'Retired' => 'secondary',
                                        ];
                                    @endphp

                                    <span class="badge badge-{{ $statusColor[$asset->status] ?? 'secondary' }}">
                                        {{ $asset->status }}
                                    </span>
                                </td>

                                {{-- CONDITION --}}
                                <td>
                                    @php
                                        $conditionColor = [
                                            'Good' => 'success',
                                            'Fair' => 'info',
                                            'Damaged' => 'warning',
                                            'Unrepairable' => 'danger',
                                        ];
                                    @endphp

                                    <span class="badge badge-{{ $conditionColor[$asset->condition] ?? 'secondary' }}">
                                        {{ $asset->condition }}
                                    </span>
                                </td>

                                {{-- ACTION --}}
                                <td class="text-center">
                                    {{-- TOMBOL EDIT --}}
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal"
                                        data-target="#modalEditAsset{{ $asset->id }}" title="Edit Asset">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    {{-- TOMBOL DELETE --}}
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="modal"
                                        data-target="#modalDeleteAsset{{ $asset->id }}" title="Hapus Asset">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            {{-- ========================================= --}}
                            {{-- MODAL EDIT ASSET --}}
                            {{-- ========================================= --}}
                            <div class="modal fade" id="modalEditAsset{{ $asset->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">

                                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                    <div class="modal-content">

                                        <form action="{{ route('hrd.assets.update', $asset->id) }}" method="POST"
                                            enctype="multipart/form-data">

                                            @csrf
                                            @method('PUT')

                                            {{-- HEADER --}}
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-edit mr-2"></i>
                                                    Edit Asset
                                                </h5>

                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>

                                            {{-- BODY --}}
                                            <div class="modal-body">
                                                <div class="row">

                                                    <div class="col-md-6 form-group">
                                                        <label>Asset Code *</label>
                                                        <input type="text" name="asset_code" class="form-control"
                                                            value="{{ $asset->asset_code }}" required>
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Kategori *</label>
                                                        <input type="text" name="category" class="form-control"
                                                            value="{{ $asset->category }}" required>
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Brand</label>
                                                        <input type="text" name="brand" class="form-control"
                                                            value="{{ $asset->brand }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Model</label>
                                                        <input type="text" name="model" class="form-control"
                                                            value="{{ $asset->model }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Serial Number</label>
                                                        <input type="text" name="serial_number" class="form-control"
                                                            value="{{ $asset->serial_number }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>PIC / Pengguna</label>
                                                        <select name="assigned_to" class="form-control">
                                                            <option value="">Belum ditentukan</option>
                                                            @foreach($employees as $employee)
                                                                <option value="{{ $employee->id }}" {{ $asset->assigned_to == $employee->id ? 'selected' : '' }}>
                                                                    {{ $employee->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Area</label>
                                                        <input type="text" name="area" class="form-control"
                                                            value="{{ $asset->area }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Lokasi</label>
                                                        <input type="text" name="location" class="form-control"
                                                            value="{{ $asset->location }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Vendor</label>
                                                        <input type="text" name="vendor" class="form-control"
                                                            value="{{ $asset->vendor }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Status *</label>
                                                        <select name="status" class="form-control" required>
                                                            @foreach(['Available', 'Active', 'Maintenance', 'Lost', 'Retired'] as $status)
                                                                <option value="{{ $status }}" {{ $asset->status == $status ? 'selected' : '' }}>
                                                                    {{ $status }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Kondisi *</label>
                                                        <select name="condition" class="form-control" required>
                                                            @foreach(['Good', 'Fair', 'Damaged', 'Unrepairable'] as $condition)
                                                                <option value="{{ $condition }}" {{ $asset->condition == $condition ? 'selected' : '' }}>
                                                                    {{ $condition }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Tanggal Pembelian</label>
                                                        <input type="date" name="acquisition_date" class="form-control"
                                                            value="{{ $asset->acquisition_date ? \Carbon\Carbon::parse($asset->acquisition_date)->format('Y-m-d') : '' }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Harga Pembelian</label>
                                                        <input type="number" name="purchase_price" class="form-control"
                                                            min="0" value="{{ $asset->purchase_price }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Tanggal Garansi Berakhir</label>
                                                        <input type="date" name="warranty_expiry" class="form-control"
                                                            value="{{ $asset->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty_expiry)->format('Y-m-d') : '' }}">
                                                    </div>

                                                    <div class="col-md-6 form-group">
                                                        <label>Foto Asset</label>
                                                        <input type="file" name="photo" class="form-control-file"
                                                            accept="image/*">

                                                        @if($asset->photo)
                                                            <small class="text-muted d-block mt-1">
                                                                Foto saat ini tersedia. Upload foto baru jika ingin
                                                                menggantinya.
                                                            </small>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-12 form-group">
                                                        <label>Deskripsi</label>
                                                        <textarea name="description" class="form-control"
                                                            rows="2">{{ $asset->description }}</textarea>
                                                    </div>

                                                    <div class="col-md-12 form-group">
                                                        <label>Catatan</label>
                                                        <textarea name="notes" class="form-control"
                                                            rows="2">{{ $asset->notes }}</textarea>
                                                    </div>

                                                </div>
                                            </div>

                                            {{-- FOOTER --}}
                                            <div class="modal-footer justify-content-between">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Batal
                                                </button>

                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-save mr-1"></i>
                                                    Update Asset
                                                </button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>


                            {{-- ========================================= --}}
                            {{-- MODAL DELETE ASSET --}}
                            {{-- ========================================= --}}
                            <div class="modal fade" id="modalDeleteAsset{{ $asset->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">

                                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                    <div class="modal-content">

                                        <div class="modal-header bg-danger">
                                            <h5 class="modal-title text-white">
                                                <i class="fas fa-trash mr-2"></i>
                                                Hapus Asset
                                            </h5>

                                            <button type="button" class="close text-white" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body text-center">

                                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>

                                            <h5>Apakah Anda yakin?</h5>

                                            <p class="text-muted mb-2">
                                                Asset berikut akan dihapus dari database:
                                            </p>

                                            <div class="bg-light rounded p-3">
                                                <strong>{{ $asset->asset_code }}</strong>
                                                <br>
                                                <span>{{ $asset->category }}</span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $asset->brand }} {{ $asset->model }}
                                                </small>
                                            </div>

                                            <p class="text-danger small mt-3 mb-0">
                                                Tindakan ini tidak dapat dibatalkan.
                                            </p>

                                        </div>

                                        <div class="modal-footer justify-content-between">

                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Batal
                                            </button>

                                            <form action="{{ route('hrd.assets.destroy', $asset->id) }}" method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Ya, Hapus
                                                </button>

                                            </form>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-2x mb-2"></i>
                                    <br>
                                    Data asset tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- FOOTER --}}
            <div class="row align-items-center mt-3">

                {{-- INFORMASI DATA --}}
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="text-muted">
                        Menampilkan
                        <strong>{{ $assets->firstItem() ?? 0 }}</strong>
                        sampai
                        <strong>{{ $assets->lastItem() ?? 0 }}</strong>
                        dari
                        <strong>{{ $assets->total() }}</strong>
                        data asset
                    </div>
                </div>

                {{-- PAGINATION --}}
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end justify-content-start">
                        {{ $assets->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- CSS --}}
    @push('css')
        <style>
            .pagination {
                margin-bottom: 0;
            }

            .pagination .page-link {
                color: #343a40;
                padding: 6px 12px;
            }

            .pagination .page-item.active .page-link {
                background-color: #343a40;
                border-color: #343a40;
                color: #fff;
            }

            .table td {
                vertical-align: middle;
            }

            .table th {
                vertical-align: middle;
                white-space: nowrap;
            }
        </style>
    @endpush


</div>

{{-- MODAL TAMBAH ASSET --}}
<div class="modal fade" id="modalAddAsset">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="{{ route('hrd.assets.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                <div class="modal-header bg-primary">
                    <h5 class="modal-title">Tambah Asset Baru</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6 form-group">
                            <label>Asset Code *</label>
                            <input type="text" name="asset_code" class="form-control" value="{{ old('asset_code') }}"
                                required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Kategori *</label>
                            <input type="text" name="category" class="form-control" value="{{ old('category') }}"
                                placeholder="Laptop, Router, HT..." required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Brand</label>
                            <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Model</label>
                            <input type="text" name="model" class="form-control" value="{{ old('model') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Serial Number</label>
                            <input type="text" name="serial_number" class="form-control"
                                value="{{ old('serial_number') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>PIC / Pengguna</label>
                            <select name="assigned_to" class="form-control">
                                <option value="">Belum ditentukan</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('assigned_to') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Area</label>
                            <input type="text" name="area" class="form-control" value="{{ old('area') }}"
                                placeholder="Contoh: Jakarta">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Lokasi</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location') }}"
                                placeholder="Contoh: Ruang HRD">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Vendor</label>
                            <input type="text" name="vendor" class="form-control" value="{{ old('vendor') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Status *</label>
                            <select name="status" class="form-control" required>
                                @foreach(['Available', 'Active', 'Maintenance', 'Lost', 'Retired'] as $status)
                                    <option value="{{ $status }}" {{ old('status', 'Available') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Kondisi *</label>
                            <select name="condition" class="form-control" required>
                                @foreach(['Good', 'Fair', 'Damaged', 'Unrepairable'] as $condition)
                                    <option value="{{ $condition }}" {{ old('condition', 'Good') == $condition ? 'selected' : '' }}>
                                        {{ $condition }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Tanggal Pembelian</label>
                            <input type="date" name="acquisition_date" class="form-control"
                                value="{{ old('acquisition_date') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Harga Pembelian</label>
                            <input type="number" name="purchase_price" class="form-control"
                                value="{{ old('purchase_price') }}" min="0">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Tanggal Garansi Berakhir</label>
                            <input type="date" name="warranty_expiry" class="form-control"
                                value="{{ old('warranty_expiry') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Foto Asset</label>
                            <input type="file" name="photo" class="form-control-file" accept="image/*">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control"
                                rows="2">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Catatan</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Asset
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@stop