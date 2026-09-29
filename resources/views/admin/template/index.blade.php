@extends('adminlte::page')

@section('title', 'Daftar Template Tersimpan')

@section('content_header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>Daftar Template Excel</h1>
    </div>
    <div class="col-sm-6 text-right">
        <a href="{{ route('admin.template.upload') }}" class="btn btn-primary disabled">
            <i class="fas fa-upload mr-1"></i> Upload Template Baru
        </a>
    </div>
</div>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">File di dalam folder <code>storage/app/templates</code></h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Nama File</th>
                            <th>Ukuran File</th>
                            <th>Terakhir Diperbarui</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $index => $template)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-file-excel text-success mr-2"></i>
                                    <strong>{{ $template['name'] }}</strong>
                                </td>
                                <td>{{ $template['size'] }}</td>
                                <td>{{ $template['updated_at'] }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.template.download', $template['name']) }}"
                                        class="btn btn-sm btn-info" title="Download Template">
                                        <i class="fas fa-download"></i> Unduh
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada template yang di-upload.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop