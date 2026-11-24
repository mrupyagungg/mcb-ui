<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-file-excel mr-2"></i>
            Import Data Assets
        </h3>
    </div>

    <div class="card-body">

        <p class="text-muted">
            Upload data asset perusahaan menggunakan file Excel
            dengan format yang sudah ditentukan.
        </p>

        <form action="{{ route('hrd.assets.import') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="form-group">
                <label for="assetExcel">Pilih File Excel</label>

                <div class="custom-file">
                    <input type="file" name="file" id="assetExcel" class="custom-file-input" accept=".xlsx,.xls"
                        required>

                    <label class="custom-file-label" for="assetExcel">
                        Pilih file Excel
                    </label>
                </div>

                @error('file')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-success"
                onclick="return confirm('Import data asset dari Excel sekarang?')">

                <i class="fas fa-upload mr-1"></i>
                Import Data
            </button>

        </form>

        <hr>

        <h6><strong>Format kolom Excel</strong></h6>

        <div class="table-responsive">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Kolom</th>
                        <th>Nama Kolom Excel</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>B</td>
                        <td>Kategori</td>
                        <td>Wajib diisi</td>
                    </tr>
                    <tr>
                        <td>C</td>
                        <td>Brand</td>
                        <td>Opsional</td>
                    </tr>
                    <tr>
                        <td>D</td>
                        <td>Model</td>
                        <td>Opsional</td>
                    </tr>
                    <tr>
                        <td>E</td>
                        <td>PIC</td>
                        <td>Dicocokkan dengan nama karyawan</td>
                    </tr>
                    <tr>
                        <td>F</td>
                        <td>Area</td>
                        <td>Opsional</td>
                    </tr>
                    <tr>
                        <td>G</td>
                        <td>Vendor</td>
                        <td>Opsional</td>
                    </tr>
                    <tr>
                        <td>H</td>
                        <td>Status Saat Ini</td>
                        <td>Diubah ke format status database</td>
                    </tr>
                    <tr>
                        <td>I</td>
                        <td>Deskripsi</td>
                        <td>Opsional</td>
                    </tr>
                    <tr>
                        <td>J</td>
                        <td>Asset Code</td>
                        <td>Dibuat otomatis oleh sistem</td>
                    </tr>
                    <tr>
                        <td>K</td>
                        <td>Serial Number</td>
                        <td>Opsional</td>
                    </tr>
                    <tr>
                        <td>L</td>
                        <td>Date</td>
                        <td>Opsional</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <small class="text-muted">
            Format file: XLSX atau XLS. Maksimal 10 MB.
        </small>
    </div>
</div>

<script>
    document.getElementById('assetExcel').addEventListener('change', function () {
        let fileName = this.files.length
            ? this.files[0].name
            : 'Pilih file Excel';

        this.nextElementSibling.textContent = fileName;
    });
</script>