<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>PT MEGANTARA CIPTA BERSAUDARA - Construction Company Website</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="Construction Company Website Template" name="keywords">
    <meta content="Construction Company Website Template" name="description">

    <!-- Favicon -->
    <link href="{{ asset('img/logo/logo-removebg-preview.png') }}" rel="icon">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('lib/flaticon/font/flaticon.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/slick/slick.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/slick/slick-theme.css') }}" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <!-- link map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
</head>
<style>
    /* Membuat Top Bar dan Nav Bar tetap stay di atas saat di-scroll */
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        width: 100%;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<body>
    <div class="wrapper">

        <div class="sticky-header">
            <!-- Top Bar Start -->
            <div class="top-bar">
                <div class="container-fluid">
                    <div class="row align-items-center">

                        <!-- Kiri: Nama perusahaan -->
                        <div class="col-lg-6 col-md-12">
                            <div class="logo" style="display: center; align-items: center; min-height: 90px;">
                                <a href="index.html" style="text-decoration: none; color: inherit; text-align: center;">
                                    <h4 style="margin: 0; line-height: 1.1; font-weight: 700; color: #0b1f3a;">
                                        PT MEGANTARA CIPTA BERSAUDARA
                                    </h4>
                                    <small
                                        style="text-align: center; display: block; margin-top: 2px; line-height: 1.1; color: #ffffff; font-weight: 600;">
                                        Connecting Network
                                    </small>
                                </a>
                            </div>
                        </div>

                        <!-- Kanan: 2 item -->
                        <div class="col-lg-6 d-none d-lg-block">
                            <div class="row">

                                <!-- Item 1 -->
                                <div class="col-6">
                                    <div class="top-bar-item"
                                        style="display: flex; align-items: center; min-height: 90px;">
                                        <div class="top-bar-icon" style="margin-right: 14px;">
                                            <i class="flaticon-calendar"></i>
                                        </div>
                                        <div class="top-bar-text">
                                            <h3 style="margin: 0 0 4px; font-size: 18px;">Opening Hour</h3>
                                            <p style="margin: 0; font-size: 14px;">Mon - Fri, 08:00 - 17:00</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Item 2 -->
                                <div class="col-6">
                                    <div class="top-bar-item"
                                        style="display: flex; align-items: center; min-height: 90px;">
                                        <div class="top-bar-icon" style="margin-right: 14px;">
                                            <i class="flaticon-call"></i>
                                        </div>
                                        <div class="top-bar-text">
                                            <h3 style="margin: 0 0 4px; font-size: 18px;">Admin Call</h3>
                                            <p style="margin: 0; font-size: 14px;">0213889170</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Top Bar End -->

            <!-- Nav Bar Start -->
            <div class="nav-bar">
                <div class="container-fluid">
                    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
                        <a href="index.html" class="navbar-brand"
                            style="padding: 0; display: flex; align-items: center;">
                            <img src="{{ asset('img/logo/logo-removebg-preview.png') }}" alt="MCB Logo"
                                style="height: 55px; width: auto; object-fit: contain;">
                        </a> <button type="button" class="navbar-toggler" data-toggle="collapse"
                            data-target="#navbarCollapse">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                            <div class="navbar-nav mr-auto">
                                <!-- Menu Home -->
                                <a href="{{ url('/') }}"
                                    class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>

                                <!-- Menu About -->
                                <a href="{{ url('/about') }}"
                                    class="nav-item nav-link {{ request()->is('about') ? 'active' : '' }}">About</a>

                                <!-- Menu Service -->
                                <a href="{{ url('/service') }}"
                                    class="nav-item nav-link {{ request()->is('service') ? 'active' : '' }}">Service</a>

                                <!-- Menu Team -->
                                <a href="{{ url('/team') }}"
                                    class="nav-item nav-link {{ request()->is('team') ? 'active' : '' }}">Team</a>

                                <!-- Menu Project / Portfolio -->
                                <a href="{{ url('/portfolio') }}"
                                    class="nav-item nav-link {{ request()->is('portfolio') ? 'active' : '' }}">Project</a>

                                <!-- Menu Dropdown Pages (Opsional jika ingin dropdown ikut aktif jika halamannya dibuka) -->
                                <div class="nav-item dropdown">
                                    <a href="#"
                                        class="nav-link dropdown-toggle {{ request()->is('blog', 'single') ? 'active' : '' }}"
                                        data-toggle="dropdown">Pages</a>
                                    <div class="dropdown-menu">
                                        <a href="{{ url('/blog') }}" class="dropdown-item">Blog Page</a>
                                        <a href="{{ url('/single') }}" class="dropdown-item">Single Page</a>
                                    </div>
                                </div>

                                <!-- Menu Contact -->
                                <a href="{{ url('/contact') }}"
                                    class="nav-item nav-link {{ request()->is('contact') ? 'active' : '' }}">Contact</a>
                            </div>

                            <div class="ml-auto">
                                @auth
                                    <a class="btn btn-success" href="{{ url('/dashboard') }}">Dashboard</a>
                                @else
                                    <a class="btn btn-primary" href="{{ url('/login') }}">Login</a>
                                @endauth
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
        <!-- Nav Bar End -->