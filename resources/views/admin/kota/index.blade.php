@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Data Kota</h1>

    <div class="card shadow">

        <div class="card-header">
            <a href="{{ route('admin.kota.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Kota
            </a>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Kota</th>
                            <th>Provinsi</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    @foreach($kota as $item)

                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $item->nama_kota }}</td>

                            <td>{{ $item->provinsi }}</td>

                            <td>

                                <a href="{{ route('admin.kota.edit',$item->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.kota.destroy',$item->id) }}" method="POST" class="d-inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data kota?')">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                </form>

                            </td>
                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>
@endsection