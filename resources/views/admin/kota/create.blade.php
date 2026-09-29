@extends('adminlte::page')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Tambah Kota
    </h1>

    <div class="card shadow">

        <div class="card-header">
            <h6 class="font-weight-bold text-primary">
                Form Kota
            </h6>
        </div>


        <div class="card-body">

            <form action="{{ route('admin.kota.store') }}" method="POST">

                @csrf


                <div class="form-group">

                    <label>Nama Kota</label>

                    <input type="text"
                           name="nama_kota"
                           class="form-control"
                           placeholder="Contoh: Bandung"
                           required>

                </div>


                <div class="form-group">

                    <label>Provinsi</label>

                    <input type="text"
                           name="provinsi"
                           class="form-control"
                           placeholder="Contoh: Jawa Barat"
                           required>

                </div>
                <div class="form-group">

                    <label>Alamat Homebase</label>

                    <input type="text"
                           name="alamat"
                           class="form-control"
                           placeholder="Contoh: JL. Raya Bandung No. 123">

                </div>


                <a href="{{ route('admin.kota') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>


                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan
                </button>


            </form>

        </div>

    </div>

</div>
@endsection