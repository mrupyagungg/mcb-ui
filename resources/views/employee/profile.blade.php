@extends('adminlte::page')

@section('title', 'Edit Profile')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h1 class="font-weight-bold mb-1">
                <i class="fas fa-user-edit text-primary mr-2"></i>Edit Profile
            </h1>
            <p class="text-muted mb-0">
                Kelola informasi pribadi dan keamanan akun Anda.
            </p>
        </div>
    </div>
@stop

@section('content')

    {{-- ALERT SUCCESS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    {{-- ALERT ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <strong>Terjadi kesalahan!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <div class="row">

        {{-- PROFILE INFORMATION --}}
        <div class="col-lg-8">

            <div class="card card-primary card-outline shadow-sm">

                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-user-circle mr-2"></i>
                        Informasi Profile
                    </h3>
                </div>

                <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- PHOTO PROFILE --}}
                        <div class="text-center mb-4">
                            <div class="profile-photo-wrapper mx-auto mb-3">
                                <img id="profilePreview"
                                    src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('vendor/adminlte/dist/img/user2-160x160.jpg') }}"
                                    class="profile-photo" alt="Profile Photo">
                            </div>

                            <label for="photo" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-camera mr-1"></i>
                                Ganti Foto
                            </label>

                            <input type="file" name="photo" id="photo" class="d-none"
                                accept="image/png,image/jpeg">

                            <p class="text-muted small mt-2 mb-0">
                                Format JPG atau PNG. Maksimal 2 MB.
                            </p>
                        </div>

                        <hr>

                        {{-- NAME --}}
                        <div class="form-group">
                            <label for="name">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                </div>

                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" required>
                            </div>

                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- EMAIL --}}
                        <div class="form-group">
                            <label for="email">
                                Email <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                </div>

                                <input type="email" name="email" id="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $user->email) }}" placeholder="Masukkan email" required>
                            </div>

                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- PHONE --}}
                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                </div>

                                <input type="text" name="phone" id="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $user->phone) }}" placeholder="Masukkan nomor telepon">
                            </div>

                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- ADDRESS --}}
                        <div class="form-group">
                            <label for="address">Alamat</label>

                            <textarea name="address" id="address" rows="3" class="form-control @error('address') is-invalid @enderror"
                                placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>

                            @error('address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <button type="reset" class="btn btn-default mr-2">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </button>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

            {{-- CHANGE PASSWORD --}}
            <div class="card card-warning card-outline shadow-sm">

                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-lock mr-2"></i>
                        Keamanan Akun
                    </h3>
                </div>

                <form action="{{ route('employee.profile.password') }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        <p class="text-muted">
                            Pastikan password baru Anda sulit ditebak dan
                            tidak digunakan pada akun lain.
                        </p>

                        {{-- CURRENT PASSWORD --}}
                        <div class="form-group">
                            <label for="current_password">
                                Password Lama
                            </label>

                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password"
                                    class="form-control" placeholder="Masukkan password lama" required>

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default toggle-password"
                                        data-target="current_password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            @error('current_password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- NEW PASSWORD --}}
                        <div class="form-group">
                            <label for="password">
                                Password Baru
                            </label>

                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control"
                                    placeholder="Minimal 8 karakter" required>

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default toggle-password"
                                        data-target="password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- CONFIRM PASSWORD --}}
                        <div class="form-group mb-0">
                            <label for="password_confirmation">
                                Konfirmasi Password Baru
                            </label>

                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control" placeholder="Ulangi password baru" required>

                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default toggle-password"
                                        data-target="password_confirmation">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key mr-1"></i>
                            Perbarui Password
                        </button>
                    </div>

                </form>

            </div>

        </div>

        {{-- DIGITAL EMPLOYEE ID CARD --}}
        <div class="col-lg-4">

            <div class="card card-primary card-outline shadow-sm">

                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-id-card mr-2"></i>
                        Digital Employee ID Card
                    </h3>
                </div>

                <div class="card-body">

                    <div class="employee-id-card" id="employeeIdCard">
                        {{-- TEMPLATE ASLI MCB --}}
                        <img src="{{ asset('img/id/id-card-template.png') }}" class="id-card-template"
                            alt="MCB Employee ID Card">

                        {{-- FOTO EMPLOYEE --}}
                        <div class="id-card-photo">
                            <img id="idCardPhoto"
                                src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('vendor/adminlte/dist/img/user2-160x160.jpg') }}"
                                alt="Employee Photo">
                        </div>

                        {{-- NAMA EMPLOYEE --}}
                        <div class="id-card-name">
                            {{ $user->name }}
                        </div>

                        {{-- POSITION --}}
                        <div class="id-card-position">
                            {{ $user->position ?? 'Employee' }}
                        </div>

                        {{-- ID EMPLOYEE --}}
                        <div class="id-card-number">
                            {{ $user->employee_id ?? '-' }}
                        </div>
                    </div>
                    {{-- DOWNLOAD ID CARD --}}
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-primary btn-sm" id="downloadIdCard">
                            <i class="fas fa-download mr-1"></i>
                            Download ID Card
                        </button>
                    </div>
                    <div class="text-center mt-3">
                        <span class="badge badge-success">
                            <i class="fas fa-check-circle mr-1"></i>
                            Employee Identity
                        </span>
                    </div>

                    <div class="alert alert-info mt-3 mb-0">
                        <small>
                            <i class="fas fa-info-circle mr-1"></i>
                            Foto kartu identitas mengikuti foto profile.
                            Nama, jabatan, dan ID Employee diambil otomatis
                            dari database perusahaan.
                        </small>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@stop
@section('css')
    <style>
        .profile-photo-wrapper {
            width: 170px;
            height: 170px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid #dee2e6;
        }

        .profile-photo {
            width: 110%;
            height: 110%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        .employee-id-card {
            position: relative;
            width: 110%;
            max-width: 435px;
            aspect-ratio: 638 / 1024;
            margin: 0 auto;
            overflow: hidden;
            background: #f8fbfd;
        }

        /* TEMPLATE */
        .id-card-template {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: fill;
            z-index: 1;
        }

        /* FOTO EMPLOYEE */
        .id-card-photo {
            position: absolute;
            z-index: 2;
            left: 24.8%;
            top: 32.5%;
            width: 50.2%;
            height: 33%;
            overflow: hidden;
            border-radius: 50%;
        }

        .id-card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            display: block;
        }

        /* NAMA EMPLOYEE */
        .id-card-name {
            position: absolute;
            z-index: 3;
            left: 15%;
            top: 72%;
            width: 70%;
            height: 4.5%;
            background: #f8fbfd;

            display: flex;
            justify-content: center;
            align-items: center;

            text-align: center;
            color: #000;

            font-family: 'Poppins', sans-serif;
            font-size: clamp(16px, 4vw, 29px);
            font-weight: 500;
            letter-spacing: 0.5px;
            line-height: 1.1;

            overflow-wrap: anywhere;
        }

        /* POSITION */
        .id-card-position {
            position: absolute;
            z-index: 3;
            left: 15%;
            top: 78.2%;
            width: 70%;
            height: 3.5%;
            background: #f8fbfd;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #0006aa;
            font-family: Arial, sans-serif;
            font-size: clamp(14px, 3.5vw, 24px);
            font-weight: 700;
            line-height: 1.1;
            overflow-wrap: anywhere;
        }

        /* ID EMPLOYEE */
        .id-card-number {
            position: absolute;
            z-index: 3;
            left: 15%;
            top: 84%;
            width: 70%;
            height: 3.5%;
            background: #f8fbfd;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #000;
            font-family: Arial, sans-serif;
            font-size: clamp(15px, 3.5vw, 25px);
            font-weight: 900;
            line-height: 1.1;
            overflow-wrap: anywhere;
        }

        @media (max-width: 575px) {
            .employee-id-card {
                max-width: 350px;
            }
        }
    </style>
@stop

@section('js')
    <script>
        // ==========================================
        // PREVIEW FOTO PROFILE DAN ID CARD
        // ==========================================

        const photoInput = document.getElementById('photo');

        if (photoInput) {
            photoInput.addEventListener('change', function(event) {

                const file = event.target.files[0];

                if (!file) return;

                // Validasi format foto
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Format foto harus JPG atau PNG.');
                    this.value = '';
                    return;
                }

                // Validasi ukuran maksimal 2 MB
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto maksimal 2 MB.');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(e) {

                    // Preview foto profile
                    const profilePreview = document.getElementById('profilePreview');

                    if (profilePreview) {
                        profilePreview.src = e.target.result;
                    }

                    // Preview foto pada ID Card
                    const idCardPhoto = document.getElementById('idCardPhoto');

                    if (idCardPhoto) {
                        idCardPhoto.src = e.target.result;
                    }

                };

                reader.readAsDataURL(file);
            });
        }


        // ==========================================
        // TOGGLE PASSWORD VISIBILITY
        // ==========================================

        document.querySelectorAll('.toggle-password').forEach(function(button) {

            button.addEventListener('click', function() {

                const input = document.getElementById(this.dataset.target);
                const icon = this.querySelector('i');

                if (!input || !icon) return;

                if (input.type === 'password') {

                    input.type = 'text';

                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');

                } else {

                    input.type = 'password';

                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');

                }

            });

        });
        // ==========================================
        // DOWNLOAD DIGITAL EMPLOYEE ID CARD
        // ==========================================

        const downloadButton = document.getElementById('downloadIdCard');

        if (downloadButton) {
            downloadButton.addEventListener('click', async function() {

                const card = document.getElementById('employeeIdCard');

                const originalText = this.innerHTML;

                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Membuat kartu...';

                try {
                    // Tunggu gambar selesai dimuat
                    const images = Array.from(card.querySelectorAll('img'));

                    await Promise.all(images.map(img => {
                        if (img.complete && img.naturalWidth > 0) {
                            return Promise.resolve();
                        }

                        return new Promise((resolve, reject) => {
                            img.onload = resolve;
                            img.onerror = reject;
                        });
                    }));

                    // Konversi kartu menjadi gambar
                    const canvas = await html2canvas(card, {
                        scale: 3,
                        useCORS: true,
                        backgroundColor: '#f8fbfd',
                        logging: false
                    });

                    // Proses download
                    const link = document.createElement('a');

                    link.download = 'ID-Card-{{ Str::slug($user->name) }}.png';
                    link.href = canvas.toDataURL('image/png');

                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                } catch (error) {
                    console.error(error);
                    alert('Gagal mengunduh ID Card. Silakan coba kembali.');
                } finally {
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            });
        }
    </script>
@stop
