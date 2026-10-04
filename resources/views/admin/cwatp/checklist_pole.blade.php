@extends('adminlte::page')

@section('title', 'Form Checklist Pole')

@section('content_header')
@stop

@section('content')

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-12">

            <form action="{{ route('admin.cwatp.export-pole') }}" method="POST" id="formATP">
                @csrf

                <input type="hidden" name="template_type" id="template_type" value="distribusi">

                <div class="card card-outline card-success shadow-sm border-0">

                    {{-- ===================================================== --}}
                    {{-- HEADER --}}
                    {{-- ===================================================== --}}
                    <div class="card-header bg-white py-3 border-bottom">

                        <div class="d-flex align-items-center">

                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3"
                                style="width:46px;height:46px;">
                                <i class="fas fa-clipboard-check fa-lg"></i>
                            </div>

                            <div>
                                <h5 class="card-title font-weight-bold m-0 text-dark">
                                    Form Checklist Pole
                                </h5>

                                <div class="mt-2">
                                    <small class="text-muted">
                                        Lengkapi data pole dan checklist pemeriksaan sebelum generate ATP.
                                    </small>
                                </div>
                            </div>

                        </div>

                    </div>

                    {{-- ===================================================== --}}
                    {{-- INFORMASI UMUM --}}
                    {{-- ===================================================== --}}
                    <div class="card-header bg-white py-3 border-bottom">

                        <h5 class="card-title font-weight-bold text-success mb-0">
                            <i class="fas fa-info-circle mr-2"></i>
                            Informasi Umum
                        </h5>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            {{-- REGION --}}
                            <div class="col-md-6 form-group">

                                <label>
                                    Region
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="region" class="form-control" value="{{ old('region') }}"
                                    required placeholder="Contoh: Regional Jabodetabek">

                            </div>

                            {{-- CLUSTER / ROUTE --}}
                            <div class="col-md-6 form-group">

                                <label>
                                    Cluster / Route
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="cluster_route" class="form-control"
                                    value="{{ old('cluster_route') }}" required
                                    placeholder="Contoh: Cluster A / Route 1">

                            </div>

                            {{-- LINK FROM --}}
                            <div class="col-md-6 form-group">

                                <label>
                                    Link From
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="link_from" class="form-control" value="{{ old('link_from') }}"
                                    required placeholder="Contoh: STO Gambir">

                            </div>

                            {{-- LINK TO --}}
                            <div class="col-md-6 form-group">

                                <label>
                                    Link To
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text" name="link_to" class="form-control" value="{{ old('link_to') }}"
                                    required placeholder="Contoh: ODC Menteng">

                            </div>

                            {{-- ADDRESS FROM --}}
                            <div class="col-md-6 form-group">

                                <label>
                                    Address From
                                    <span class="text-danger">*</span>
                                </label>

                                <textarea name="address_from" class="form-control" rows="2" required
                                    placeholder="Alamat asal penarikan kabel/pole">{{ old('address_from') }}</textarea>

                            </div>

                            {{-- ADDRESS TO --}}
                            <div class="col-md-6 form-group">

                                <label>
                                    Address To
                                    <span class="text-danger">*</span>
                                </label>

                                <textarea name="address_to" class="form-control" rows="2" required
                                    placeholder="Alamat tujuan penarikan kabel/pole">{{ old('address_to') }}</textarea>

                            </div>

                        </div>

                    </div>

                    {{-- ===================================================== --}}
                    {{-- TAB --}}
                    {{-- ===================================================== --}}
                    <div class="card-header bg-light p-2 border-bottom">

                        <ul class="nav nav-pills nav-fill" id="atpTab" role="tablist">

                            {{-- DISTRIBUSI --}}
                            <li class="nav-item">

                                <a class="nav-link active font-weight-bold" id="distribusi-tab" data-toggle="tab"
                                    href="#distribusi" role="tab">

                                    <i class="fas fa-network-wired mr-2"></i>
                                    ATP Distribusi

                                </a>

                            </li>

                            {{-- SUBFEEDER --}}
                            <li class="nav-item">

                                <a class="nav-link font-weight-bold" id="subfeeder-tab" data-toggle="tab"
                                    href="#subfeeder" role="tab">

                                    <i class="fas fa-project-diagram mr-2"></i>
                                    ATP Subfeeder

                                </a>

                            </li>

                        </ul>

                    </div>

                    {{-- ===================================================== --}}
                    {{-- TAB CONTENT --}}
                    {{-- ===================================================== --}}
                    <div class="tab-content">

                        {{-- ================================================= --}}
                        {{-- ATP DISTRIBUSI --}}
                        {{-- ================================================= --}}
                        <div class="tab-pane fade show active" id="distribusi" role="tabpanel">

                            <div class="card-body">

                                {{-- ========================================= --}}
                                {{-- POLE --}}
                                {{-- ========================================= --}}
                                <div class="card border shadow-none mb-4">

                                    {{-- POLE HEADER --}}
                                    <div class="card-header bg-success text-white">

                                        <div class="d-flex justify-content-between align-items-center">

                                            <div>

                                                <h6 class="mb-0 font-weight-bold">

                                                    <i class="fas fa-ruler-vertical mr-2"></i>

                                                    Pole Height & Quantity

                                                </h6>

                                                <small>
                                                    Tambahkan tipe pole dan jumlah pole yang digunakan.
                                                </small>

                                            </div>

                                            <button type="button" class="btn btn-light btn-sm font-weight-bold"
                                                id="btnTambahPole">

                                                <i class="fas fa-plus mr-1"></i>

                                                Tambah Tipe Pole

                                            </button>

                                        </div>

                                    </div>

                                    {{-- POLE TABLE --}}
                                    <div class="card-body p-0">

                                        <div class="table-responsive">

                                            <table class="table table-bordered table-hover mb-0" id="tablePole"
                                                style="min-width:1100px;">

                                                <thead class="bg-light">

                                                    <tr>

                                                        <th class="text-center align-middle" style="width:60px;">
                                                            No.
                                                        </th>

                                                        <th class="align-middle" style="min-width:210px;">
                                                            Tipe / Tinggi Pole
                                                        </th>

                                                        <th class="text-center align-middle" style="width:140px;">
                                                            Jumlah Pole
                                                        </th>

                                                        <th class="text-center align-middle" style="width:140px;">
                                                            Move Pole
                                                        </th>

                                                        <th class="text-center align-middle" style="width:140px;">
                                                            Cut Pole
                                                        </th>

                                                        <th class="text-center align-middle" style="width:140px;">
                                                            Label Pole
                                                        </th>

                                                        <th class="text-center align-middle" style="width:160px;">
                                                            Accessories Pole
                                                        </th>

                                                        <th class="text-center align-middle" style="width:160px;">
                                                            Foundation Pole
                                                        </th>

                                                        <th class="text-center align-middle" style="width:80px;">
                                                            Aksi
                                                        </th>

                                                    </tr>

                                                </thead>

                                                <tbody id="poleContainer">

                                                    {{-- ================================= --}}
                                                    {{-- BARIS AWAL --}}
                                                    {{-- ================================= --}}
                                                    <tr class="pole-row">

                                                        {{-- NOMOR --}}
                                                        <td
                                                            class="text-center align-middle nomor-pole font-weight-bold">
                                                            1
                                                        </td>

                                                        {{-- HEIGHT --}}
                                                        <td>

                                                            <select name="poles[0][height]"
                                                                class="form-control form-control-sm" required>

                                                                <option value="">
                                                                    -- Pilih Tipe Pole --
                                                                </option>

                                                                <option value="7 Mtr 2.5 inch">
                                                                    7 Mtr 2.5 inch
                                                                </option>

                                                                <option value="7 Mtr 3 inch">
                                                                    7 Mtr 3 inch
                                                                </option>

                                                                <option value="7 Mtr 4 inch">
                                                                    7 Mtr 4 inch
                                                                </option>

                                                                <option value="9 Mtr 4 inch">
                                                                    9 Mtr 4 inch
                                                                </option>

                                                            </select>

                                                        </td>

                                                        {{-- JUMLAH --}}
                                                        <td>

                                                            <input type="number" name="poles[0][jumlah]"
                                                                class="form-control form-control-sm jumlah-pole" min="1"
                                                                placeholder="Jumlah" required>

                                                        </td>

                                                        {{-- MOVE --}}
                                                        <td>

                                                            <select name="poles[0][move_pole]"
                                                                class="form-control form-control-sm" required>

                                                                <option value="NO">
                                                                    NO
                                                                </option>

                                                                <option value="YES">
                                                                    YES
                                                                </option>

                                                            </select>

                                                        </td>

                                                        {{-- CUT --}}
                                                        <td>

                                                            <select name="poles[0][cut_pole]"
                                                                class="form-control form-control-sm" required>

                                                                <option value="NO">
                                                                    NO
                                                                </option>

                                                                <option value="YES">
                                                                    YES
                                                                </option>

                                                            </select>

                                                        </td>

                                                        {{-- LABEL --}}
                                                        <td>

                                                            <select name="poles[0][label_pole]"
                                                                class="form-control form-control-sm" required>

                                                                <option value="YES">
                                                                    YES
                                                                </option>

                                                                <option value="NO">
                                                                    NO
                                                                </option>

                                                            </select>

                                                        </td>

                                                        {{-- ACCESSORIES --}}
                                                        <td>

                                                            <select name="poles[0][accessories_pole]"
                                                                class="form-control form-control-sm" required>

                                                                <option value="YES">
                                                                    YES
                                                                </option>

                                                                <option value="NO">
                                                                    NO
                                                                </option>

                                                            </select>

                                                        </td>

                                                        {{-- FOUNDATION --}}
                                                        <td>

                                                            <select name="poles[0][foundation_pole]"
                                                                class="form-control form-control-sm" required>

                                                                <option value="YES">
                                                                    YES
                                                                </option>

                                                                <option value="NO">
                                                                    NO
                                                                </option>

                                                            </select>

                                                        </td>

                                                        {{-- DELETE --}}
                                                        <td class="text-center align-middle">

                                                            <button type="button"
                                                                class="btn btn-outline-danger btn-sm btn-hapus-pole"
                                                                disabled title="Hapus">

                                                                <i class="fas fa-trash"></i>

                                                            </button>

                                                        </td>

                                                    </tr>

                                                </tbody>

                                                {{-- TOTAL --}}
                                                <tfoot class="bg-light">

                                                    <tr>

                                                        <th colspan="2" class="text-right align-middle">

                                                            Total Pole

                                                        </th>

                                                        <th>

                                                            <input type="text" id="totalPole"
                                                                class="form-control form-control-sm font-weight-bold text-center"
                                                                value="0" readonly>

                                                        </th>

                                                        <th colspan="6"></th>

                                                    </tr>

                                                </tfoot>

                                            </table>

                                        </div>

                                    </div>

                                </div>

                                {{-- ========================================= --}}
                                {{-- INFORMATION --}}
                                {{-- ========================================= --}}
                                <div class="alert alert-light border mb-0">

                                    <div class="d-flex">

                                        <i class="fas fa-info-circle text-success mr-2 mt-1"></i>

                                        <div>

                                            <strong>
                                                Informasi
                                            </strong>

                                            <div class="small text-muted">

                                                Pastikan tipe dan jumlah pole sudah sesuai.
                                                Periksa kembali seluruh checklist sebelum melakukan generate ATP.

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- ================================================= --}}
                        {{-- ATP SUBFEEDER --}}
                        {{-- ================================================= --}}
                        <div class="tab-pane fade" id="subfeeder" role="tabpanel">

                            <div class="card-body">

                                <div class="alert alert-info mb-0">

                                    <i class="fas fa-info-circle mr-2"></i>

                                    Form ATP Subfeeder dapat digunakan pada tab ini.

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- ===================================================== --}}
                    {{-- FOOTER --}}
                    {{-- ===================================================== --}}
                    <div class="card-footer bg-white border-top py-3">

                        <div class="d-flex justify-content-between align-items-center">

                            <div class="text-muted small">

                                <i class="fas fa-shield-alt mr-1 text-success"></i>

                                Pastikan data yang dimasukkan sudah benar.

                            </div>

                            <div>

                                {{-- RESET --}}
                                <button type="reset" class="btn btn-outline-secondary mr-2" id="btnReset">

                                    <i class="fas fa-undo mr-1"></i>

                                    Reset

                                </button>

                                {{-- GENERATE --}}
                                <button type="submit" class="btn btn-success px-4 font-weight-bold" id="btnGenerate">

                                    <i class="fas fa-file-excel mr-2"></i>

                                    Generate ATP

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>
    </div>
</div>

@stop

@section('js')

{{-- ========================================================= --}}
{{-- SWEETALERT2 --}}
{{-- ========================================================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const container = document.getElementById('poleContainer');
        const btnTambah = document.getElementById('btnTambahPole');
        const totalPole = document.getElementById('totalPole');
        const form = document.getElementById('formATP');
        const btnGenerate = document.getElementById('btnGenerate');
        const btnReset = document.getElementById('btnReset');

        let poleIndex = 1;

        // =========================================================
        // UPDATE NOMOR
        // =========================================================
        function updateNomor() {

            const rows = container.querySelectorAll('.pole-row');

            rows.forEach(function (row, index) {

                row.querySelector('.nomor-pole').textContent =
                    index + 1;

            });

            const deleteButtons =
                container.querySelectorAll('.btn-hapus-pole');

            deleteButtons.forEach(function (button) {

                button.disabled =
                    rows.length === 1;

            });

            updateTotal();
        }

        // =========================================================
        // UPDATE TOTAL
        // =========================================================
        function updateTotal() {

            let total = 0;

            container
                .querySelectorAll('.jumlah-pole')
                .forEach(function (input) {

                    total += parseInt(input.value) || 0;

                });

            totalPole.value = total;
        }

        // =========================================================
        // CREATE POLE ROW
        // =========================================================
        function createPoleRow(index) {

            const row =
                document.createElement('tr');

            row.className = 'pole-row';

            row.innerHTML = `

            <td class="text-center align-middle nomor-pole font-weight-bold">
                -
            </td>

            <td>

                <select name="poles[${index}][height]"
                    class="form-control form-control-sm"
                    required>

                    <option value="">
                        -- Pilih Tipe Pole --
                    </option>

                    <option value="7 Mtr 2.5 inch">
                        7 Mtr 2.5 inch
                    </option>

                    <option value="7 Mtr 3 inch">
                        7 Mtr 3 inch
                    </option>

                    <option value="7 Mtr 4 inch">
                        7 Mtr 4 inch
                    </option>

                    <option value="9 Mtr 4 inch">
                        9 Mtr 4 inch
                    </option>

                </select>

            </td>

            <td>

                <input type="number"
                    name="poles[${index}][jumlah]"
                    class="form-control form-control-sm jumlah-pole"
                    min="1"
                    placeholder="Jumlah"
                    required>

            </td>

            <td>

                <select name="poles[${index}][move_pole]"
                    class="form-control form-control-sm"
                    required>

                    <option value="NO">
                        NO
                    </option>

                    <option value="YES">
                        YES
                    </option>

                </select>

            </td>

            <td>

                <select name="poles[${index}][cut_pole]"
                    class="form-control form-control-sm"
                    required>

                    <option value="NO">
                        NO
                    </option>

                    <option value="YES">
                        YES
                    </option>

                </select>

            </td>

            <td>

                <select name="poles[${index}][label_pole]"
                    class="form-control form-control-sm"
                    required>

                    <option value="YES">
                        YES
                    </option>

                    <option value="NO">
                        NO
                    </option>

                </select>

            </td>

            <td>

                <select name="poles[${index}][accessories_pole]"
                    class="form-control form-control-sm"
                    required>

                    <option value="YES">
                        YES
                    </option>

                    <option value="NO">
                        NO
                    </option>

                </select>

            </td>

            <td>

                <select name="poles[${index}][foundation_pole]"
                    class="form-control form-control-sm"
                    required>

                    <option value="YES">
                        YES
                    </option>

                    <option value="NO">
                        NO
                    </option>

                </select>

            </td>

            <td class="text-center align-middle">

                <button type="button"
                    class="btn btn-outline-danger btn-sm btn-hapus-pole"
                    title="Hapus">

                    <i class="fas fa-trash"></i>

                </button>

            </td>
        `;

            container.appendChild(row);

            updateNomor();
        }

        // =========================================================
        // TAMBAH TIPE POLE
        // =========================================================
        btnTambah.addEventListener('click', function () {

            createPoleRow(poleIndex);

            poleIndex++;

        });

        // =========================================================
        // HAPUS TIPE POLE
        // =========================================================
        container.addEventListener('click', function (event) {

            const button =
                event.target.closest('.btn-hapus-pole');

            if (!button) return;

            const row =
                button.closest('.pole-row');

            if (!row) return;

            row.remove();

            updateNomor();

        });

        // =========================================================
        // UPDATE TOTAL SAAT INPUT
        // =========================================================
        container.addEventListener('input', function (event) {

            if (
                event.target.classList.contains('jumlah-pole')
            ) {

                updateTotal();

            }

        });

        // =========================================================
        // GENERATE EXCEL - AJAX
        // =========================================================
        form.addEventListener('submit', async function (event) {

            event.preventDefault();

            // -----------------------------------------------------
            // VALIDASI HTML
            // -----------------------------------------------------
            if (!form.checkValidity()) {

                form.reportValidity();

                return;

            }

            // -----------------------------------------------------
            // TOTAL POLE
            // -----------------------------------------------------
            const total =
                parseInt(totalPole.value) || 0;

            if (total < 1) {

                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Minimal harus ada 1 pole.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ffc107'
                });

                return;
            }

            // -----------------------------------------------------
            // DISABLE BUTTON
            // -----------------------------------------------------
            btnGenerate.disabled = true;

            btnGenerate.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"
                role="status"
                aria-hidden="true"></span>
            Generating ATP...
        `;

            // -----------------------------------------------------
            // SWEETALERT LOADING
            // -----------------------------------------------------
            Swal.fire({
                title: 'Memproses Checklist Pole...',
                text: 'Mohon tunggu, sistem sedang membuat file Excel.',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: function () {

                    Swal.showLoading();

                }
            });

            try {

                // -------------------------------------------------
                // FORM DATA
                // -------------------------------------------------
                const formData =
                    new FormData(form);

                // -------------------------------------------------
                // FETCH
                // -------------------------------------------------
                const response =
                    await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept':
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        }
                    });

                // -------------------------------------------------
                // CEK RESPONSE
                // -------------------------------------------------
                if (!response.ok) {

                    let message =
                        'Gagal membuat Checklist Pole.';

                    const contentType =
                        response.headers.get('Content-Type') || '';

                    if (
                        contentType.includes('application/json')
                    ) {

                        const errorData =
                            await response.json();

                        if (errorData.message) {

                            message =
                                errorData.message;

                        }

                    } else {

                        if (response.status === 422) {

                            message =
                                'Data yang dimasukkan belum valid.';

                        } else if (response.status === 419) {

                            message =
                                'Session telah berakhir. Silakan refresh halaman.';

                        } else if (response.status === 403) {

                            message =
                                'Anda tidak memiliki izin untuk melakukan proses ini.';

                        } else if (response.status === 500) {

                            message =
                                'Terjadi kesalahan pada server saat membuat file Excel.';

                        }

                    }

                    throw new Error(message);
                }

                // -------------------------------------------------
                // NAMA FILE
                // -------------------------------------------------
                let filename =
                    'ATP_Checklist_Pole_' +
                    new Date().getTime() +
                    '.xlsx';

                const disposition =
                    response.headers.get(
                        'Content-Disposition'
                    );

                if (
                    disposition &&
                    disposition.indexOf('filename=') !== -1
                ) {

                    const matches =
                        /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/
                            .exec(disposition);

                    if (
                        matches &&
                        matches[1]
                    ) {

                        filename =
                            matches[1]
                                .replace(/['"]/g, '');

                    }

                }

                // -------------------------------------------------
                // BLOB
                // -------------------------------------------------
                const blob =
                    await response.blob();

                if (!blob || blob.size === 0) {

                    throw new Error(
                        'File Excel yang dihasilkan kosong.'
                    );

                }

                // -------------------------------------------------
                // DOWNLOAD
                // -------------------------------------------------
                const url =
                    window.URL.createObjectURL(blob);

                const a =
                    document.createElement('a');

                a.href = url;

                a.download =
                    filename;

                document.body.appendChild(a);

                a.click();

                a.remove();

                window.URL.revokeObjectURL(url);

                // -------------------------------------------------
                // SUCCESS
                // -------------------------------------------------
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Checklist Pole berhasil dibuat dan file Excel telah diunduh.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });

            } catch (error) {

                console.error(
                    'Generate Checklist Pole Error:',
                    error
                );

                // -------------------------------------------------
                // ERROR
                // -------------------------------------------------
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memproses',
                    text:
                        error.message ||
                        'Terjadi kesalahan saat membuat file Excel.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });

            } finally {

                // -------------------------------------------------
                // ENABLE BUTTON
                // -------------------------------------------------
                btnGenerate.disabled = false;

                btnGenerate.innerHTML = `
                <i class="fas fa-file-excel mr-2"></i>
                Generate ATP
            `;

            }

        });

        // =========================================================
        // RESET
        // =========================================================
        btnReset.addEventListener('click', function () {

            setTimeout(function () {

                updateNomor();

                poleIndex =
                    container.querySelectorAll('.pole-row').length;

                btnGenerate.disabled = false;

                btnGenerate.innerHTML = `
                <i class="fas fa-file-excel mr-2"></i>
                Generate ATP
            `;

            }, 100);

        });

        // =========================================================
        // INITIALIZE
        // =========================================================
        updateNomor();

    });
</script>

@stop