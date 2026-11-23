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

                <div class="card-body text-center">

                    {{-- CAMERA STATUS --}}
                    <div class="camera-status">
                        <small class="text-muted" id="cameraStatus">
                            <i class="fas fa-circle text-secondary mr-1"></i>
                            Kamera belum aktif
                        </small>
                    </div>

                    {{-- =================================================
                    CAMERA
                    ================================================== --}}
                    <div class="camera-wrapper position-relative mx-auto">

                        <video id="camera"
                               autoplay
                               playsinline
                               muted>
                        </video>

                        {{-- FACE FRAME --}}
                        <div class="face-frame position-absolute"></div>

                    </div>

                    {{-- =================================================
                    CAMERA BUTTON
                    ================================================== --}}
                    @if (!$absensi || !$absensi->jam_keluar)

                        <button type="button"
                                class="btn btn-outline-success btn-camera"
                                id="btnStartCamera">

                            <i class="fas fa-camera mr-2"></i>
                            Aktifkan Kamera

                        </button>

                    @endif


                    {{-- =================================================
                    ABSEN BUTTON
                    ================================================== --}}
                    @if (!$absensi)

                        <button type="button"
                                class="btn btn-success btn-lg btn-block btn-absen"
                                id="btnAbsen"
                                disabled>

                            <i class="fas fa-fingerprint mr-2"></i>
                            ABSEN MASUK

                        </button>

                    @elseif (!$absensi->jam_keluar)

                        <button type="button"
                                class="btn btn-primary btn-lg btn-block btn-absen"
                                id="btnAbsen"
                                disabled>

                            <i class="fas fa-sign-out-alt mr-2"></i>
                            ABSEN KELUAR

                        </button>

                    @else

                        <button type="button"
                                class="btn btn-secondary btn-lg btn-block btn-absen"
                                disabled>

                            <i class="fas fa-check-double mr-2"></i>
                            ABSEN SELESAI

                        </button>

                    @endif


                    {{-- =================================================
                    STATUS ABSENSI
                    ================================================== --}}
                    <div class="attendance-info">

                        @if (!$absensi)

                            <h4 class="font-weight-bold text-dark mb-2">
                                Absen Masuk
                            </h4>

                            <div class="mb-2">
                                <span id="jam"
                                      class="font-weight-bold text-dark">
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


                    {{-- SNAPSHOT --}}
                    <canvas id="snapshot" style="display:none;"></canvas>

                </div>
            </div>
        </div>


        {{-- =========================================================
        CARD INFORMASI ABSENSI
        ========================================================== --}}
        <div class="col-lg-6 mb-4">

            <div class="card card-outline card-primary shadow-sm">

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
                            <i class="fas fa-user-circle text-primary user-icon"></i>
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
                    <div class="row mb-2">

                        <div class="col-5 text-muted">
                            <i class="far fa-calendar-alt mr-2"></i>
                            Tanggal
                        </div>

                        <div class="col-7 font-weight-bold"
                             id="tanggal">
                            -
                        </div>

                    </div>


                    {{-- JALAN --}}
                    <div class="row mb-2">

                        <div class="col-5 text-muted">
                            <i class="fas fa-road mr-2"></i>
                            Jalan
                        </div>

                        <div class="col-7 font-weight-bold"
                             id="jalan">
                            Mendeteksi lokasi...
                        </div>

                    </div>


                    {{-- KOTA --}}
                    <div class="row mb-2">

                        <div class="col-5 text-muted">
                            <i class="fas fa-city mr-2"></i>
                            Kota
                        </div>

                        <div class="col-7 font-weight-bold"
                             id="kota">
                            Mendeteksi lokasi...
                        </div>

                    </div>


                    {{-- KOORDINAT --}}
                    <div class="row mb-2">

                        <div class="col-5 text-muted">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Koordinat
                        </div>

                        <div class="col-7">

                            <div>
                                <small class="text-muted">
                                    Latitude
                                </small>

                                <div id="latitude"
                                     class="font-weight-bold">
                                    -
                                </div>
                            </div>

                            <div class="mt-2">

                                <small class="text-muted">
                                    Longitude
                                </small>

                                <div id="longitude"
                                     class="font-weight-bold">
                                    -
                                </div>

                            </div>

                        </div>

                    </div>

                    <hr>


                    {{-- LOCATION STATUS --}}
                    <div class="alert alert-light border mb-2"
                         id="locationStatus">

                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Sedang mengambil lokasi...

                    </div>


                    {{-- MAP --}}
                    <div id="map"></div>


                    {{-- GOOGLE MAPS --}}
                    <a href="#"
                       target="_blank"
                       class="btn btn-outline-primary btn-block mt-2"
                       id="btnMaps"
                       style="display:none;">

                        <i class="fas fa-map-marked-alt mr-2"></i>
                        Buka di Google Maps

                    </a>

                </div>
            </div>
        </div>

    </div>
</div>


{{-- =========================================================
LEAFLET
========================================================= --}}
<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">


{{-- =========================================================
CUSTOM CSS
========================================================= --}}
<style>

    /* =========================================================
       CAMERA
       ========================================================= */

    .camera-wrapper {
        width: 100%;
        max-width: 500px;
    }

    #camera {
        display: block;
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 12px;
        background: #111;
        transform: scaleX(-1);
    }

    .face-frame {
        top: 50%;
        left: 50%;
        width: 220px;
        height: 280px;
        transform: translate(-50%, -50%);
        border: 3px solid #28a745;
        border-radius: 50%;
        pointer-events: none;
    }


    /* =========================================================
       CAMERA BUTTON
       ========================================================= */

    .btn-camera {
        width: 100%;
        margin-top: 10px;
        min-height: 44px;
    }

    .btn-absen {
        margin-top: 8px;
        min-height: 48px;
    }


    /* =========================================================
       CAMERA STATUS
       ========================================================= */

    .camera-status {
        margin-bottom: 8px;
    }


    /* =========================================================
       ATTENDANCE INFO
       ========================================================= */

    .attendance-info {
        margin-top: 12px;
    }

    #jam {
        font-size: 24px;
    }


    /* =========================================================
       USER ICON
       ========================================================= */

    .user-icon {
        font-size: 70px;
    }


    /* =========================================================
       MAP
       ========================================================= */

    #map {
        width: 100%;
        height: 220px;
        border-radius: 10px;
        overflow: hidden;
        display: none;
    }


    /* =========================================================
       MOBILE / HP
       ========================================================= */

    @media (max-width: 767.98px) {

        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        .row {
            margin-left: -5px;
            margin-right: -5px;
        }

        .col-lg-6 {
            padding-left: 5px;
            padding-right: 5px;
        }

        .card {
            margin-bottom: 10px !important;
        }

        .card-header {
            padding: 10px 12px;
        }

        .card-body {
            padding: 12px;
        }


        /* CAMERA LEBIH PENDEK */

        .camera-wrapper {
            width: 100%;
            max-width: 100%;
        }

        #camera {
            height: 300px;
            min-height: 0;
            border-radius: 10px;
        }


        /* FRAME WAJAH */

        .face-frame {
            width: 145px;
            height: 185px;
            border-width: 2px;
        }


        /* STATUS KAMERA */

        .camera-status {
            margin-bottom: 6px;
        }


        /* TOMBOL AKTIFKAN KAMERA */

        .btn-camera {
            width: 100%;
            margin-top: 8px;
            min-height: 42px;
            font-size: 14px;
        }


        /* TOMBOL ABSEN */

        .btn-absen {
            width: 100%;
            margin-top: 7px;
            min-height: 46px;
            font-size: 15px;
        }


        /* INFORMASI ABSENSI */

        .attendance-info {
            margin-top: 10px;
        }

        .attendance-info h4 {
            font-size: 18px;
        }

        .attendance-info p {
            font-size: 13px;
        }


        /* JAM */

        #jam {
            font-size: 22px !important;
        }


        /* CARD INFORMASI */

        .user-icon {
            font-size: 55px;
        }

        .card-body h4 {
            font-size: 18px;
        }


        /* MAP */

        #map {
            height: 180px;
        }


        /* LOCATION STATUS */

        #locationStatus {
            font-size: 13px;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

    }


    /* =========================================================
       HP KECIL
       ========================================================= */

    @media (max-width: 380px) {

        #camera {
            height: 200px;
        }

        .face-frame {
            width: 130px;
            height: 165px;
        }

        .btn-camera {
            min-height: 40px;
            font-size: 13px;
        }

        .btn-absen {
            min-height: 44px;
            font-size: 14px;
        }

        .card-body {
            padding: 10px;
        }

    }

</style>

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

                locationStatus.className =
                    'alert alert-danger border';

                locationStatus.innerHTML =
                    '<i class="fas fa-times-circle mr-2"></i>' +
                    'Browser tidak mendukung GPS.';

                return;
            }

            locationStatus.className =
                'alert alert-light border';

            locationStatus.innerHTML =
                '<i class="fas fa-spinner fa-spin mr-2"></i>' +
                'Sedang mengambil lokasi...';

            navigator.geolocation.getCurrentPosition(

                function (position) {

                    currentLatitude =
                        position.coords.latitude;

                    currentLongitude =
                        position.coords.longitude;

                    /*
                    |--------------------------------------------------------------------------
                    | TAMPILKAN KOORDINAT
                    |--------------------------------------------------------------------------
                    */

                    if (latitude) {

                        latitude.textContent =
                            currentLatitude.toFixed(7);

                    }

                    if (longitude) {

                        longitude.textContent =
                            currentLongitude.toFixed(7);

                    }

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS LOKASI
                    |--------------------------------------------------------------------------
                    */

                    locationStatus.className =
                        'alert alert-success border';

                    locationStatus.innerHTML =
                        '<i class="fas fa-check-circle mr-2"></i>' +
                        'Lokasi berhasil ditemukan.';

                    /*
                    |--------------------------------------------------------------------------
                    | GOOGLE MAPS
                    |--------------------------------------------------------------------------
                    */

                    if (btnMaps) {

                        btnMaps.href =
                            'https://www.google.com/maps?q=' +
                            currentLatitude +
                            ',' +
                            currentLongitude;

                        btnMaps.style.display =
                            'block';
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | OPENSTREETMAP
                    |--------------------------------------------------------------------------
                    */

                    updateMap(
                        currentLatitude,
                        currentLongitude
                    );

                    /*
                    |--------------------------------------------------------------------------
                    | CEK SIAP ABSEN
                    |--------------------------------------------------------------------------
                    */

                    checkReady();

                    /*
                    |--------------------------------------------------------------------------
                    | REVERSE GEOCODING
                    |--------------------------------------------------------------------------
                    */

                    fetch(
                        'https://nominatim.openstreetmap.org/reverse' +
                        '?format=json' +
                        '&lat=' +
                        currentLatitude +
                        '&lon=' +
                        currentLongitude +
                        '&zoom=18' +
                        '&addressdetails=1',
                        {
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    )
                        .then(function (response) {

                            if (!response.ok) {

                                throw new Error(
                                    'Reverse geocoding gagal.'
                                );
                            }

                            return response.json();

                        })
                        .then(function (data) {

                            console.log(
                                'Reverse Geocoding:',
                                data
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | JIKA ADDRESS TIDAK ADA
                            |--------------------------------------------------------------------------
                            */

                            if (!data.address) {

                                if (jalan) {

                                    jalan.textContent =
                                        data.display_name ||
                                        'Lokasi tidak diketahui';

                                }

                                if (kota) {

                                    kota.textContent =
                                        'Tidak diketahui';

                                }

                                return;
                            }

                            const address =
                                data.address;

                            /*
                            |--------------------------------------------------------------------------
                            | NAMA LOKASI
                            |--------------------------------------------------------------------------
                            */

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

                            /*
                            |--------------------------------------------------------------------------
                            | TAMPILKAN LOKASI
                            |--------------------------------------------------------------------------
                            */

                            if (jalan) {

                                if (namaLokasi) {

                                    jalan.textContent =
                                        namaLokasi;

                                } else if (
                                    data.display_name
                                ) {

                                    jalan.textContent =
                                        data.display_name;

                                } else {

                                    jalan.textContent =
                                        'Lokasi tidak diketahui';
                                }
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | KOTA
                            |--------------------------------------------------------------------------
                            */

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

                            console.error(
                                'Reverse Geocoding Error:',
                                error
                            );

                            if (jalan) {

                                jalan.textContent =
                                    'Lokasi tidak dapat diketahui';

                            }

                            if (kota) {

                                kota.textContent =
                                    'Tidak dapat mengambil kota';

                            }
                        });

                },

                function (error) {

                    console.error(
                        'GPS Error:',
                        error
                    );

                    locationStatus.className =
                        'alert alert-danger border';

                    locationStatus.innerHTML =
                        '<i class="fas fa-exclamation-circle mr-2"></i>' +
                        'Lokasi tidak dapat diperoleh. ' +
                        'Pastikan GPS aktif dan izin lokasi diberikan.';

                    if (btnAbsen) {

                        btnAbsen.disabled = true;

                    }

                },

                {
                    enableHighAccuracy: true,
                    timeout: 15000,
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnAllowLocation = document.getElementById('btnAllowLocation');
    const locationStatus = document.getElementById('locationStatus');

    if (!btnAllowLocation || !locationStatus) return;

    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) ||
            (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
    }

    function showLocationDenied() {

        if (isIOS()) {

            locationStatus.className = 'alert alert-danger border text-left';

            locationStatus.innerHTML = `
                <div class="text-center mb-2">
                    <i class="fas fa-map-marker-alt text-danger"
                       style="font-size:30px;"></i>
                </div>

                <strong class="d-block text-center mb-2">
                    Lokasi Tidak Diizinkan
                </strong>

                <small>
                    Anda menolak akses lokasi. Untuk menggunakan
                    fitur absensi, silakan aktifkan kembali izin lokasi
                    untuk Safari.
                </small>

                <hr>

                <strong>
                    Cara mengaktifkan lokasi di iPhone:
                </strong>

                <ol class="mt-2 mb-0 pl-4">
                    <li>
                        Buka <strong>Settings</strong>
                    </li>
                    <li>
                        Pilih <strong>Privacy & Security</strong>
                    </li>
                    <li>
                        Pilih <strong>Location Services</strong>
                    </li>
                    <li>
                        Pilih <strong>Safari Websites</strong>
                    </li>
                    <li>
                        Pilih <strong>While Using the App</strong>
                    </li>
                    <li>
                        Pastikan <strong>Precise Location</strong>
                        dalam keadaan <strong>ON</strong>
                    </li>
                </ol>

                <div class="alert alert-warning mt-3 mb-0">
                    <i class="fas fa-info-circle mr-1"></i>
                    Setelah selesai, kembali ke halaman absensi
                    kemudian tekan tombol
                    <strong>Coba Lagi</strong>.
                </div>
            `;

            btnAllowLocation.innerHTML = `
                <i class="fas fa-redo mr-2"></i>
                Coba Lagi
            `;

        } else {

            locationStatus.className = 'alert alert-danger border text-left';

            locationStatus.innerHTML = `
                <div class="text-center mb-2">
                    <i class="fas fa-map-marker-alt text-danger"
                       style="font-size:30px;"></i>
                </div>

                <strong class="d-block text-center mb-2">
                    Lokasi Tidak Diizinkan
                </strong>

                <small>
                    Silakan aktifkan GPS dan izin lokasi
                    untuk browser Anda melalui pengaturan HP.
                </small>

                <hr>

                <strong>
                    Pastikan:
                </strong>

                <ul class="mt-2 mb-0">
                    <li>GPS/Lokasi HP dalam keadaan ON</li>
                    <li>Izin lokasi browser diberikan</li>
                    <li>Gunakan koneksi HTTPS</li>
                </ul>
            `;

            btnAllowLocation.innerHTML = `
                <i class="fas fa-redo mr-2"></i>
                Coba Lagi
            `;
        }

        btnAllowLocation.disabled = false;
    }

    function getLocation() {

        if (!navigator.geolocation) {

            locationStatus.className = 'alert alert-danger border';

            locationStatus.innerHTML = `
                <i class="fas fa-times-circle mr-2"></i>
                Browser Anda tidak mendukung fitur lokasi.
            `;

            return;
        }

        locationStatus.className = 'alert alert-warning border';

        locationStatus.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Meminta izin lokasi...
        `;

        navigator.geolocation.getCurrentPosition(

            function (position) {

                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                window.currentLatitude = latitude;
                window.currentLongitude = longitude;

                locationStatus.className =
                    'alert alert-success border';

                locationStatus.innerHTML = `
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Lokasi berhasil diaktifkan.</strong>
                    <br>
                    <small>
                        ${latitude.toFixed(6)},
                        ${longitude.toFixed(6)}
                    </small>
                `;

                btnAllowLocation.innerHTML = `
                    <i class="fas fa-check mr-2"></i>
                    Lokasi Aktif
                `;

                btnAllowLocation.classList.remove(
                    'btn-outline-danger'
                );

                btnAllowLocation.classList.add(
                    'btn-success'
                );

                btnAllowLocation.disabled = true;

            },

            function (error) {

                console.log(
                    'GPS Error:',
                    error.code,
                    error.message
                );

                /*
                 * 1 = PERMISSION_DENIED
                 * 2 = POSITION_UNAVAILABLE
                 * 3 = TIMEOUT
                 */

                if (error.code === 1) {
                    showLocationDenied();
                    return;
                }

                if (error.code === 2) {

                    locationStatus.className =
                        'alert alert-danger border';

                    locationStatus.innerHTML = `
                        <i class="fas fa-location-crosshairs mr-2"></i>
                        <strong>GPS tidak tersedia.</strong>
                        <br>
                        Aktifkan Location/GPS pada perangkat
                        Anda kemudian coba kembali.
                    `;

                } else if (error.code === 3) {

                    locationStatus.className =
                        'alert alert-warning border';

                    locationStatus.innerHTML = `
                        <i class="fas fa-clock mr-2"></i>
                        <strong>Lokasi terlalu lama diperoleh.</strong>
                        <br>
                        Pastikan GPS aktif dan coba kembali.
                    `;

                } else {

                    locationStatus.className =
                        'alert alert-danger border';

                    locationStatus.innerHTML = `
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Tidak dapat memperoleh lokasi.
                    `;
                }

                btnAllowLocation.innerHTML = `
                    <i class="fas fa-redo mr-2"></i>
                    Coba Lagi
                `;

                btnAllowLocation.disabled = false;
            },

            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    }

    btnAllowLocation.addEventListener('click', function () {

        btnAllowLocation.disabled = true;

        btnAllowLocation.innerHTML = `
            <i class="fas fa-spinner fa-spin mr-2"></i>
            Memeriksa Lokasi...
        `;

        getLocation();
    });
});
</script>
@stop