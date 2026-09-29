@extends('adminlte::page')

@section('title', 'Upload Template Excel')

@section('content_header')
<h1>Upload Template Excel ATP</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
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

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Form Update Template</h3>
            </div>
            <form action="{{ route('admin.template.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="template_type">Jenis Template</label>
                        <select name="template_type" id="template_type"
                            class="form-control @error('template_type') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Template --</option>
                            <option value="distribusi">Distribusi (Template ATP - Full Foto.xlsx)</option>
                            <option value="subfeeder">Subfeeder (Template ATP - Full Foto-SF.xlsx)</option>
                        </select>
                        @error('template_type')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="template_file">File Excel (.xlsx)</label>
                        <input type="file" name="template_file" id="template_file"
                            class="form-control-file @error('template_file') is-invalid @enderror" accept=".xlsx"
                            required>
                        <small class="form-text text-muted">Maksimal ukuran file: 10MB.</small>
                        @error('template_file')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload mr-1"></i> Upload
                        Template</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop