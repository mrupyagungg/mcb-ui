@extends('adminlte::page')

@section('title', 'Absensi - ' . config('app.name'))



@section('content')
<br>
<div class="container-fluid">
    <div class="row">

        {{-- =========================================================
        CARD KAMERA / ABSEN
        ========================================================== --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-success shadow-sm h-100">

                {{-- HEADER --}}
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-camera mr-2 text-success"></i>

                        @if (!$absensi)
                            Absen Masuk
                        @elseif (!$absensi->jam_keluar)
                            Absen Keluar
                        @else
                            Absensi Selesai
                        @endif
                    </h3>
                </div>

                <div class="card-body">

                    {{-- =================================================
                    CAMERA STATUS
                    ================================================== --}}
                    <div class="text-center mb-2">
                        <small class="text-muted" id="cameraStatus">
                            <i class="fas fa-circle text-secondary mr-1"></i>
                            Kamera belum aktif
                        </small>
                    </div>

                    {{-- =================================================
                    LOCATION STATUS
                    ================================================== --}}
                    <div class="mb-3">
                        <div class="alert alert-light border mb-0" id="locationStatus">
                            <i class="fas fa-map-marker-alt text-danger mr-2"></i>
                            Lokasi belum diizinkan
                        </div>
                    </div>

                    {{-- =================================================
                    CAMERA
                    ================================================== --}}
                    <div class="position-relative mx-auto" style="max-width:500px;">

                        <video id="camera" autoplay playsinline muted style="
                                   width:100%;
                                   min-height:320px;
                                   object-fit:cover;
                                   border-radius:12px;
                                   background:#111;
                                   transform:scaleX(-1);
                                   display:block;
                               ">
                        </video>

                        {{-- FACE FRAME --}}
                        <div class="position-absolute" style="
                                 top:50%;
                                 left:50%;
                                 transform:translate(-50%,-50%);
                                 width:220px;
                                 height:280px;
                                 border:3px solid #28a745;
                                 border-radius:50%;
                                 pointer-events:none;
                             ">
                        </div>

                    </div>

                    {{-- =================================================
                    TITLE / STATUS ABSENSI
                    ================================================== --}}
                    <div class="text-center mt-4">

                        @if (!$absensi)

                            <h4 class="font-weight-bold text-dark mb-2">
                                Absen Masuk
                            </h4>

                            <div class="mb-2">
                                <span id="jam" class="font-weight-bold text-dark" style="font-size:26px;">
                                    00:00:00
                                </span>
                            </div>

                            <p class="text-muted mb-2">
                                Pastikan wajah berada di dalam area kamera
                            </p>

                        @elseif (!$absensi->jam_keluar)

                            <h4 class="font-weight-bold text-dark mb-2">
                                Absen Keluar
                            </h4>

                            <p class="text-muted mb-3">
                                Anda sudah melakukan absen masuk pada
                                <strong>{{ $absensi->jam_masuk }}</strong>
                            </p>

                            @if ($absensi->status_masuk === 'terlambat')

                                <div class="alert alert-danger mb-3">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Anda tercatat <strong>TERLAMBAT</strong>
                                </div>

                            @else

                                <div class="alert alert-success mb-3">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Absen masuk <strong>TEPAT WAKTU</strong>
                                </div>

                            @endif

                        @else

                            <h4 class="font-weight-bold text-success mb-2">
                                Absensi Selesai
                            </h4>

                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle mr-2"></i>
                                Anda sudah melakukan absen masuk
                                dan keluar hari ini.
                            </div>

                        @endif

                    </div>

                    {{-- =================================================
                    CAMERA BUTTON
                    ================================================== --}}
                    @if (!$absensi || !$absensi->jam_keluar)

                        <div class="mt-3">

                            <button type="button" class="btn btn-outline-success btn-block" id="btnStartCamera">

                                <i class="fas fa-camera mr-2"></i>
                                Aktifkan Kamera

                            </button>

                        </div>

                    @endif

                    {{-- =================================================
                    ABSEN BUTTON
                    ================================================== --}}
                    @if (!$absensi)

                        <button type="button" class="btn btn-success btn-lg btn-block mt-2" id="btnAbsen" disabled>

                            <i class="fas fa-fingerprint mr-2"></i>
                            ABSEN MASUK

                        </button>

                    @elseif (!$absensi->jam_keluar)

                        <button type="button" class="btn btn-primary btn-lg btn-block mt-3" id="btnAbsen" disabled>

                            <i class="fas fa-sign-out-alt mr-2"></i>
                            ABSEN KELUAR

                        </button>

                    @else

                        <button type="button" class="btn btn-secondary btn-lg btn-block mt-2" disabled>

                            <i class="fas fa-check-double mr-2"></i>
                            ABSEN SELESAI

                        </button>

                    @endif

                    {{-- SNAPSHOT --}}
                    <canvas id="snapshot" style="display:none;"></canvas>

                </div>
            </div>
        </div>


        {{-- =========================================================
        CARD INFORMASI ABSENSI
        ========================================================== --}}
        <div class="col-lg-6 mb-4">
            <div class="card card-outline card-primary shadow-sm h-100">

                {{-- HEADER --}}
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-user-check mr-2 text-primary"></i>
                        Informasi Absensi
                    </h3>
                </div>

                <div class="card-body">

                    {{-- USER --}}
                    <div class="text-center mb-4">

                        <div class="mb-2">
                            <i class="fas fa-user-circle text-primary" style="font-size:70px;"></i>
                        </div>

                        <h4 class="font-weight-bold mb-1">
                            {{ auth()->user()->name ?? 'User' }}
                        </h4>

                        <span class="badge badge-primary">
                            {{ auth()->user()->role ?? 'Employee' }}
                        </span>

                    </div>

                    <hr>

                    {{-- TANGGAL --}}
                    <div class="row mb-3">
                        <div class="col-5 text-muted">
                            <i class="far fa-calendar-alt mr-2"></i>
                            Tanggal
                        </div>

                        <div class="col-7 font-weight-bold" id="tanggal">
                            -
                        </div>
                    </div>

                    {{-- JALAN --}}
                    <div class="row mb-3">
                        <div class="col-5 text-muted">
                            <i class="fas fa-road mr-2"></i>
                            Jalan
                        </div>

                        <div class="col-7 font-weight-bold" id="jalan">
                            Mendeteksi lokasi...
                        </div>
                    </div>

                    {{-- KOTA --}}
                    <div class="row mb-3">
                        <div class="col-5 text-muted">
                            <i class="fas fa-city mr-2"></i>
                            Kota
                        </div>

                        <div class="col-7 font-weight-bold" id="kota">
                            Mendeteksi lokasi...
                        </div>
                    </div>

                    {{-- KOORDINAT --}}
                    <div class="row mb-3">

                        <div class="col-5 text-muted">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Koordinat
                        </div>

                        <div class="col-7">

                            <div>
                                <small class="text-muted">
                                    Latitude
                                </small>

                                <div id="latitude" class="font-weight-bold">
                                    -
                                </div>
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">
                                    Longitude
                                </small>

                                <div id="longitude" class="font-weight-bold">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>

                    <hr>

                    {{-- MAP --}}
                    <div id="map" style="
                             width:100%;
                             height:220px;
                             border-radius:10px;
                             overflow:hidden;
                             display:none;
                         ">
                    </div>

                    {{-- GOOGLE MAPS --}}
                    <a href="#" target="_blank" class="btn btn-outline-primary btn-block mt-3" id="btnMaps"
                        style="display:none;">

                        <i class="fas fa-map-marked-alt mr-2"></i>
                        Buka di Google Maps

                    </a>

                </div>
            </div>
        </div>

    </div>
</div>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@stop

@section('css')
<style>
    #camera {
        display: block;
        width: 100%;
        min-height: 320px;
        object-fit: cover;
    }

    #btnAbsen {
        border-radius: 8px;
        font-weight: bold;
        letter-spacing: .5px;
    }

    #btnStartCamera {
        border-radius: 8px;
        font-weight: 600;
    }

    .card {
        border-radius: 10px;
    }

    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }

    #locationStatus {
        font-size: 14px;
    }
</style>
@stop

@section('js')

<script>
    document.addEventListener('DOMContentLoaded', function () {

        /*
        |--------------------------------------------------------------------------
        | ELEMENT
        |--------------------------------------------------------------------------
        */

        const camera = document.getElementById('camera');
        const btnStartCamera = document.getElementById('btnStartCamera');
        const btnAbsen = document.getElementById('btnAbsen');
        const cameraStatus = document.getElementById('cameraStatus');

        const tanggal = document.getElementById('tanggal');
        const jam = document.getElementById('jam');

        const latitude = document.getElementById('latitude');
        const longitude = document.getElementById('longitude');

        const jalan = document.getElementById('jalan');
        const kota = document.getElementById('kota');

        const locationStatus = document.getElementById('locationStatus');
        const btnMaps = document.getElementById('btnMaps');
        const canvas = document.getElementById('snapshot');

        const mapElement = document.getElementById('map');

        /*
        |--------------------------------------------------------------------------
        | VARIABLE
        |--------------------------------------------------------------------------
        */

        let stream = null;
        let currentLatitude = null;
        let currentLongitude = null;
        let attendanceMap = null;
        let attendanceMarker = null;

        /*
        |--------------------------------------------------------------------------
        | MODE ABSENSI
        |--------------------------------------------------------------------------
        */

        const attendanceMode = @json(
            !$absensi
            ? 'masuk'
            : (!$absensi->jam_keluar ? 'keluar' : 'selesai')
        );

        const endpoint = attendanceMode === 'masuk'
            ? @json(route('absensi.masuk'))
            : @json(route('absensi.keluar'));

        /*
        |--------------------------------------------------------------------------
        | JAM DAN TANGGAL
        |--------------------------------------------------------------------------
        */

        function updateClock() {

            const now = new Date();

            const dateOptions = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };

            if (tanggal) {
                tanggal.textContent =
                    now.toLocaleDateString(
                        'id-ID',
                        dateOptions
                    );
            }

            if (jam) {
                jam.textContent =
                    now.toLocaleTimeString(
                        'id-ID',
                        {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        }
                    );
            }
        }

        updateClock();
        setInterval(updateClock, 1000);

        /*
        |--------------------------------------------------------------------------
        | PETA
        |--------------------------------------------------------------------------
        */

        function updateMap(lat, lng) {

            if (!mapElement) {
                return;
            }

            if (
                typeof L === 'undefined'
            ) {
                console.error(
                    'Leaflet belum dimuat.'
                );

                return;
            }

            mapElement.style.display = 'block';

            /*
            |--------------------------------------------------------------------------
            | BUAT PETA PERTAMA KALI
            |--------------------------------------------------------------------------
            */

            if (!attendanceMap) {

                attendanceMap = L.map(
                    mapElement
                ).setView(
                    [lat, lng],
                    17
                );

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution:
                            '&copy; OpenStreetMap contributors'
                    }
                ).addTo(
                    attendanceMap
                );

                /*
                |--------------------------------------------------------------------------
                | MARKER
                |--------------------------------------------------------------------------
                */

                attendanceMarker =
                    L.marker([
                        lat,
                        lng
                    ])
                        .addTo(
                            attendanceMap
                        )
                        .bindPopup(
                            'Lokasi Anda'
                        );

            } else {

                /*
                |--------------------------------------------------------------------------
                | UPDATE POSISI PETA
                |--------------------------------------------------------------------------
                */

                attendanceMap.setView(
                    [lat, lng],
                    17
                );

                if (attendanceMarker) {

                    attendanceMarker.setLatLng([
                        lat,
                        lng
                    ]);

                }
            }

            /*
            |--------------------------------------------------------------------------
            | FIX MAP SIZE
            |--------------------------------------------------------------------------
            */

            setTimeout(function () {

                if (attendanceMap) {
                    attendanceMap.invalidateSize();
                }

            }, 300);
        }

        /*
        |--------------------------------------------------------------------------
        | AKTIFKAN KAMERA
        |--------------------------------------------------------------------------
        */

        if (btnStartCamera) {

            btnStartCamera.addEventListener(
                'click',
                async function () {

                    try {

                        if (
                            !navigator.mediaDevices ||
                            !navigator.mediaDevices.getUserMedia
                        ) {

                            throw new Error(
                                'Browser tidak mendukung kamera.'
                            );
                        }

                        stream =
                            await navigator.mediaDevices.getUserMedia({
                                video: {
                                    facingMode: 'user',
                                    width: {
                                        ideal: 1280
                                    },
                                    height: {
                                        ideal: 720
                                    }
                                },
                                audio: false
                            });

                        camera.srcObject = stream;

                        /*
                        |--------------------------------------------------------------------------
                        | TUNGGU VIDEO SIAP
                        |--------------------------------------------------------------------------
                        */

                        await camera.play();

                        btnStartCamera.innerHTML =
                            '<i class="fas fa-check mr-2"></i>' +
                            'Kamera Aktif';

                        btnStartCamera.classList.remove(
                            'btn-outline-success'
                        );

                        btnStartCamera.classList.add(
                            'btn-success'
                        );

                        btnStartCamera.disabled = true;

                        if (cameraStatus) {

                            cameraStatus.innerHTML =
                                '<i class="fas fa-circle text-success mr-1"></i>' +
                                'Kamera aktif';

                        }

                        checkReady();

                    } catch (error) {

                        console.error(
                            'Camera Error:',
                            error
                        );

                        if (cameraStatus) {

                            cameraStatus.innerHTML =
                                '<i class="fas fa-circle text-danger mr-1"></i>' +
                                'Kamera tidak dapat diakses';

                        }

                        alert(
                            'Kamera tidak dapat diakses. ' +
                            'Pastikan izin kamera diberikan.'
                        );
                    }
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GPS
        |--------------------------------------------------------------------------
        */

        function getLocation() {
            if (!navigator.geolocation) {
                if (locationStatus) {
                    locationStatus.className = 'alert alert-danger border';
                    locationStatus.innerHTML =
                        '<i class="fas fa-times-circle mr-2"></i>' +
                        'Browser tidak mendukung GPS.';
                }
                return;
            }

            if (!window.isSecureContext) {
                if (locationStatus) {
                    locationStatus.className = 'alert alert-danger border';
                    locationStatus.innerHTML =
                        '<i class="fas fa-exclamation-circle mr-2"></i>' +
                        'Akses lokasi membutuhkan koneksi HTTPS.';
                }
                return;
            }

            if (locationStatus) {
                locationStatus.className = 'alert alert-light border';
                locationStatus.innerHTML =
                    '<i class="fas fa-spinner fa-spin mr-2"></i>' +
                    'Sedang mengambil lokasi...';
            }

            if (btnAbsen) {
                btnAbsen.disabled = true;
            }

            function processLocation(position) {
                currentLatitude = position.coords.latitude;
                currentLongitude = position.coords.longitude;

                console.log('GPS berhasil:', {
                    latitude: currentLatitude,
                    longitude: currentLongitude,
                    accuracy: position.coords.accuracy
                });

                if (latitude) {
                    latitude.textContent = currentLatitude.toFixed(7);
                }

                if (longitude) {
                    longitude.textContent = currentLongitude.toFixed(7);
                }

                if (locationStatus) {
                    locationStatus.className = 'alert alert-success border';
                    locationStatus.innerHTML =
                        '<i class="fas fa-check-circle mr-2"></i>' +
                        'Lokasi berhasil ditemukan.';
                }

                if (btnMaps) {
                    btnMaps.href =
                        'https://www.google.com/maps?q=' +
                        currentLatitude + ',' +
                        currentLongitude;

                    btnMaps.style.display = 'block';
                }

                updateMap(
                    currentLatitude,
                    currentLongitude
                );

                checkReady();

                fetch(
                    'https://nominatim.openstreetmap.org/reverse' +
                    '?format=json' +
                    '&lat=' + encodeURIComponent(currentLatitude) +
                    '&lon=' + encodeURIComponent(currentLongitude) +
                    '&zoom=18' +
                    '&addressdetails=1',
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                )
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Reverse geocoding gagal.');
                        }

                        return response.json();
                    })
                    .then(function (data) {
                        console.log('Reverse Geocoding:', data);

                        if (!data || !data.address) {
                            if (jalan) {
                                jalan.textContent =
                                    data && data.display_name
                                        ? data.display_name
                                        : 'Lokasi tidak diketahui';
                            }

                            if (kota) {
                                kota.textContent = 'Tidak diketahui';
                            }

                            return;
                        }

                        const address = data.address;

                        const namaLokasi =
                            address.road ||
                            address.pedestrian ||
                            address.residential ||
                            address.service ||
                            address.footway ||
                            address.path ||
                            address.cycleway ||
                            address.neighbourhood ||
                            address.suburb ||
                            address.village ||
                            address.hamlet ||
                            address.town ||
                            address.city_district ||
                            '';

                        if (jalan) {
                            jalan.textContent =
                                namaLokasi ||
                                data.display_name ||
                                'Lokasi tidak diketahui';
                        }

                        if (kota) {
                            kota.textContent =
                                address.city ||
                                address.town ||
                                address.municipality ||
                                address.county ||
                                address.city_district ||
                                address.village ||
                                'Tidak diketahui';
                        }
                    })
                    .catch(function (error) {
                        console.error('Reverse Geocoding Error:', error);

                        if (jalan) {
                            jalan.textContent = 'Lokasi berhasil, alamat tidak tersedia';
                        }

                        if (kota) {
                            kota.textContent = 'Tidak diketahui';
                        }
                    });
            }

            function locationError(error, retry) {
                console.error('GPS Error:', {
                    code: error.code,
                    message: error.message,
                    retry: retry
                });

                if (retry) {
                    if (locationStatus) {
                        locationStatus.className = 'alert alert-warning border';
                        locationStatus.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-2"></i>' +
                            'GPS belum mendapatkan lokasi, mencoba kembali...';
                    }

                    navigator.geolocation.getCurrentPosition(
                        processLocation,
                        function (finalError) {
                            console.error('GPS Final Error:', finalError);

                            if (locationStatus) {
                                locationStatus.className =
                                    'alert alert-danger border';

                                let message =
                                    'Lokasi tidak dapat diperoleh.';

                                if (finalError.code === 1) {
                                    message =
                                        'Izin lokasi ditolak. Silakan izinkan lokasi untuk mcb-id.com pada pengaturan browser.';
                                } else if (finalError.code === 2) {
                                    message =
                                        'Lokasi tidak tersedia. Aktifkan GPS/lokasi HP dan coba lagi.';
                                } else if (finalError.code === 3) {
                                    message =
                                        'GPS timeout. Pastikan lokasi HP aktif dan coba lagi di area terbuka.';
                                }

                                locationStatus.innerHTML =
                                    '<i class="fas fa-exclamation-circle mr-2"></i>' +
                                    message;
                            }

                            if (btnAbsen) {
                                btnAbsen.disabled = true;
                            }
                        },
                        {
                            enableHighAccuracy: false,
                            timeout: 30000,
                            maximumAge: 10000
                        }
                    );

                    return;
                }

                locationError(error, true);
            }

            navigator.geolocation.getCurrentPosition(
                processLocation,
                function (error) {
                    locationError(error, true);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 30000,
                    maximumAge: 0
                }
            );
        }

        getLocation();

        /*
        |--------------------------------------------------------------------------
        | CEK SIAP ABSEN
        |--------------------------------------------------------------------------
        */

        function checkReady() {

            if (!btnAbsen) {
                return;
            }

            if (attendanceMode === 'selesai') {

                btnAbsen.disabled = true;

                return;
            }

            if (
                stream &&
                currentLatitude !== null &&
                currentLongitude !== null
            ) {

                btnAbsen.disabled = false;

            } else {

                btnAbsen.disabled = true;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PROSES ABSENSI
        |--------------------------------------------------------------------------
        */

        if (btnAbsen) {

            btnAbsen.addEventListener(
                'click',
                async function () {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK KAMERA
                    |--------------------------------------------------------------------------
                    */

                    if (!stream) {

                        alert(
                            'Silakan aktifkan kamera terlebih dahulu.'
                        );

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CEK GPS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        currentLatitude === null ||
                        currentLongitude === null
                    ) {

                        alert(
                            'Lokasi belum tersedia.'
                        );

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CEK VIDEO
                    |--------------------------------------------------------------------------
                    */

                    if (
                        camera.videoWidth === 0 ||
                        camera.videoHeight === 0
                    ) {

                        alert(
                            'Kamera belum siap. ' +
                            'Silakan tunggu beberapa detik.'
                        );

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL FOTO
                    |--------------------------------------------------------------------------
                    */

                    canvas.width =
                        camera.videoWidth;

                    canvas.height =
                        camera.videoHeight;

                    const context =
                        canvas.getContext('2d');

                    /*
                    |--------------------------------------------------------------------------
                    | MIRROR FOTO
                    |--------------------------------------------------------------------------
                    */

                    context.save();

                    context.translate(
                        canvas.width,
                        0
                    );

                    context.scale(
                        -1,
                        1
                    );

                    context.drawImage(
                        camera,
                        0,
                        0,
                        canvas.width,
                        canvas.height
                    );

                    context.restore();

                    /*
                    |--------------------------------------------------------------------------
                    | CONVERT KE JPEG
                    |--------------------------------------------------------------------------
                    */

                    const photo =
                        canvas.toDataURL(
                            'image/jpeg',
                            0.85
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | DISABLE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    btnAbsen.disabled = true;

                    const originalButton =
                        btnAbsen.innerHTML;

                    btnAbsen.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-2"></i>' +
                        'MEMPROSES...';

                    /*
                    |--------------------------------------------------------------------------
                    | KIRIM KE LARAVEL
                    |--------------------------------------------------------------------------
                    */

                    try {

                        const csrfToken =
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            );

                        if (!csrfToken) {

                            throw new Error(
                                'CSRF token tidak ditemukan.'
                            );
                        }

                        const response =
                            await fetch(
                                endpoint,
                                {
                                    method: 'POST',

                                    headers: {
                                        'X-CSRF-TOKEN':
                                            csrfToken.getAttribute(
                                                'content'
                                            ),

                                        'X-Requested-With':
                                            'XMLHttpRequest',

                                        'Accept':
                                            'application/json',

                                        'Content-Type':
                                            'application/json'
                                    },

                                    body: JSON.stringify({

                                        latitude:
                                            currentLatitude,

                                        longitude:
                                            currentLongitude,

                                        jalan:
                                            jalan
                                                ? jalan.textContent
                                                : '',

                                        kota:
                                            kota
                                                ? kota.textContent
                                                : '',

                                        photo:
                                            photo
                                    })
                                }
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | RESPONSE
                        |--------------------------------------------------------------------------
                        */

                        const data =
                            await response.json();

                        if (
                            !response.ok ||
                            !data.success
                        ) {

                            throw new Error(
                                data.message ||
                                'Absensi gagal diproses.'
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        alert(
                            data.message ||
                            'Absensi berhasil.'
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | MATIKAN KAMERA
                        |--------------------------------------------------------------------------
                        */

                        if (stream) {

                            stream
                                .getTracks()
                                .forEach(
                                    function (track) {

                                        track.stop();

                                    }
                                );

                            stream = null;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | RELOAD
                        |--------------------------------------------------------------------------
                        */

                        window.location.reload();

                    } catch (error) {

                        console.error(
                            'Absensi Error:',
                            error
                        );

                        alert(
                            error.message ||
                            'Terjadi kesalahan saat menyimpan absensi.'
                        );

                        btnAbsen.disabled = false;

                        btnAbsen.innerHTML =
                            originalButton;
                    }
                }
            );
        }

    });
</script>
@stop