<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>PT MEGANTARA CIPTA BERSAUDARA - Construction Company Website</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="PT Megantara Cipta Bersaudara, MCB, telekomunikasi, konstruksi, jaringan">
    <meta name="description" content="PT Megantara Cipta Bersaudara - Connecting Network">

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/logo/logo-removebg-preview.png') }}">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="{{ asset('lib/flaticon/font/flaticon.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/lightbox/css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/slick/slick.css') }}" rel="stylesheet">
    <link href="{{ asset('lib/slick/slick-theme.css') }}" rel="stylesheet">

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

    <!-- Template Stylesheet -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        /* =========================================================
           FLOATING WHATSAPP
        ========================================================= */
        .whatsapp-float {
            position: fixed;
            left: 20px;
            bottom: 20px;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #25D366;
            color: #fff;
            border-radius: 50%;
            font-size: 32px;
            text-decoration: none !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .30);
            z-index: 9999;
            transition: all .3s ease;
        }

        .whatsapp-float:hover {
            background: #20ba5a;
            color: #fff;
            transform: scale(1.08);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .35);
        }

        /* =========================================================
           TOP BAR
        ========================================================= */
        .top-bar {
            background: #ffc107;
        }

        .top-bar .logo {
            min-height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .top-bar .company-name {
            margin: 0;
            line-height: 1.1;
            font-weight: 700;
            color: #0b1f3a;
            font-size: clamp(16px, 2vw, 20px);
        }

        .top-bar .company-tagline {
            display: block;
            margin-top: 3px;
            line-height: 1.1;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
        }

        .top-bar-item {
            min-height: 90px;
            display: flex;
            align-items: center;
        }

        .top-bar-icon {
            margin-right: 14px;
            font-size: 26px;
            color: #0b1f3a;
        }

        .top-bar-text h3 {
            margin: 0 0 4px;
            font-size: 18px;
            font-weight: 600;
            color: #0b1f3a;
        }

        .top-bar-text p {
            margin: 0;
            font-size: 14px;
            color: #0b1f3a;
        }

        /* =========================================================
           STICKY NAVBAR
        ========================================================= */
        .sticky-header {
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 999;
        }

        .sticky-header .nav-bar {
            width: 100%;
        }

        .sticky-header .navbar {
            min-height: 65px;
            padding: 8px 20px;
        }

        /* =========================================================
           NAVBAR LOGO
        ========================================================= */
        .navbar-brand {
            display: flex !important;
            align-items: center;
            padding: 0 !important;
            margin-right: 25px;
        }

        .navbar-brand img {
            height: 45px;
            width: auto;
            object-fit: contain;
        }

        /* =========================================================
           NAVIGATION
        ========================================================= */
        .navbar-dark .navbar-nav .nav-link {
            color: rgba(255, 255, 255, .90);
            font-size: 14px;
            font-weight: 500;
            transition: all .2s ease;
        }

        .navbar-dark .navbar-nav .nav-link:hover,
        .navbar-dark .navbar-nav .nav-link.active {
            color: #ffc107;
        }

        .navbar-nav .dropdown-menu {
            border: 0;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, .15);
        }

        .navbar-nav .dropdown-item {
            font-size: 13px;
            padding: 9px 18px;
            transition: all .2s ease;
        }

        .navbar-nav .dropdown-item:hover {
            background: #ffc107;
            color: #0b1f3a;
        }

        /* =========================================================
           LOGIN BUTTON
        ========================================================= */
        .login-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-width: 90px;
            padding: 8px 17px;
            border: 1px solid rgba(255, 255, 255, .35);
            border-radius: 6px;
            background: #ffc107;
            color: #0b1f3a !important;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .2px;
            text-decoration: none !important;
            box-shadow: 0 3px 8px rgba(0, 0, 0, .15);
            transition: all .25s ease;
        }

        .login-btn i {
            font-size: 13px;
        }

        .login-btn:hover {
            background: #fff;
            color: #0b1f3a !important;
            border-color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 5px 12px rgba(0, 0, 0, .20);
        }

        /* =========================================================
           MOBILE LOGIN
        ========================================================= */
        .mobile-login {
            display: none;
        }

        /* =========================================================
           MOBILE / TABLET
        ========================================================= */
        @media (max-width: 991.98px) {

            .top-bar .logo {
                min-height: 70px;
                padding: 10px 0;
            }

            .sticky-header .navbar {
                min-height: 65px;
            }

            .navbar-brand img {
                height: 42px;
            }

            .mobile-login {
                display: block;
                margin-left: auto;
                margin-right: 10px;
                flex-shrink: 0;
            }

            .mobile-login .login-btn {
                min-width: auto;
                padding: 7px 12px;
                font-size: 12px;
            }

            .mobile-login .login-btn i {
                font-size: 12px;
            }

            .navbar-toggler {
                border: 1px solid rgba(255, 255, 255, .35);
                padding: 6px 9px;
                margin-left: 0;
                outline: none !important;
            }

            .navbar-collapse {
                margin-top: 10px;
                padding: 15px;
                background: #1c1c1c;
                border-radius: 5px;
                box-shadow: 0 5px 15px rgba(0, 0, 0, .20);
            }

            .navbar-nav .nav-link {
                padding: 10px 5px;
            }

            .navbar-nav .dropdown-menu {
                margin-top: 0;
                box-shadow: none;
            }

            .navbar-nav .dropdown-item {
                padding: 9px 15px;
            }
        }

        /* =========================================================
           SMALL MOBILE
        ========================================================= */
        @media (max-width: 575.98px) {

            .top-bar .logo {
                min-height: 65px;
                padding: 8px 10px;
            }

            .top-bar .company-name {
                font-size: 15px;
            }

            .top-bar .company-tagline {
                font-size: 11px;
            }

            .sticky-header .navbar {
                padding: 8px 12px;
            }

            .navbar-brand {
                margin-right: 0;
            }

            .navbar-brand img {
                height: 38px;
            }

            .mobile-login {
                margin-right: 7px;
            }

            .mobile-login .login-btn {
                padding: 6px 10px;
                font-size: 11px;
            }

            .mobile-login .login-btn i {
                font-size: 11px;
            }

            .navbar-toggler {
                padding: 5px 8px;
            }

            .whatsapp-float {
                left: 15px;
                bottom: 15px;
                width: 52px;
                height: 52px;
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

    <div class="wrapper">

        <!-- =========================================================
         TOP BAR
    ========================================================== -->
        <div class="top-bar">
            <div class="container-fluid">
                <div class="row align-items-center">

                    <!-- Company -->
                    <div class="col-lg-6 col-md-12 text-center text-lg-left">
                        <div class="logo">
                            <a href="{{ url('/') }}" class="text-decoration-none">
                                <h4 class="company-name">
                                    PT MEGANTARA CIPTA BERSAUDARA
                                </h4>
                                <small class="company-tagline">
                                    Connecting Network
                                </small>
                            </a>
                        </div>
                    </div>

                    <!-- Information -->
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="row">

                            <!-- Opening Hour -->
                            <div class="col-6">
                                <div class="top-bar-item">
                                    <div class="top-bar-icon">
                                        <i class="flaticon-calendar"></i>
                                    </div>

                                    <div class="top-bar-text">
                                        <h3>Opening Hour</h3>
                                        <p>Mon - Fri, 08:00 - 17:00</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Call -->
                            <div class="col-6">
                                <div class="top-bar-item">
                                    <div class="top-bar-icon">
                                        <i class="flaticon-call"></i>
                                    </div>

                                    <div class="top-bar-text">
                                        <h3>Admin Call</h3>
                                        <p>0213889170</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- =========================================================
         STICKY NAVBAR
    ========================================================== -->
        <div class="sticky-header">

            <div class="nav-bar">

                <div class="container-fluid">

                    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">

                        <!-- Logo -->
                        <a href="{{ url('/') }}" class="navbar-brand" title="PT Megantara Cipta Bersaudara">

                            <img src="{{ asset('img/logo/logo-removebg-preview.png') }}"
                                alt="PT Megantara Cipta Bersaudara">
                        </a>

                        <!-- Login Mobile / Tablet -->
                        <div class="mobile-login">

                            @auth

                                @if (auth()->user()->role === 'hrd')
                                    <a class="login-btn" href="{{ url('/hrd/dashboard') }}" title="HRD Dashboard">

                                        <i class="fas fa-user-circle"></i>
                                        <span>Login</span>

                                    </a>
                                @elseif (auth()->user()->role === 'employee')
                                    <a class="login-btn" href="{{ url('/employee/dashboard') }}" title="Employee Dashboard">

                                        <i class="fas fa-user-circle"></i>
                                        <span>Login</span>

                                    </a>
                                @else
                                    <a class="login-btn" href="{{ url('/') }}" title="Home">

                                        <i class="fas fa-user-circle"></i>
                                        <span>Login</span>

                                    </a>
                                @endif
                            @else
                                <a class="login-btn" href="{{ url('/login') }}" title="Login">

                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Login</span>

                                </a>

                            @endauth

                        </div>

                        <!-- Hamburger -->
                        <button type="button" class="navbar-toggler" data-toggle="collapse"
                            data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                            aria-label="Toggle navigation">

                            <span class="navbar-toggler-icon"></span>

                        </button>

                        <!-- Navbar Content -->
                        <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">

                            <!-- Navigation -->
                            <div class="navbar-nav mr-auto">

                                <!-- Home -->
                                <a href="{{ url('/') }}"
                                    class="nav-item nav-link {{ request()->is('/') ? 'active' : '' }}">
                                    Home
                                </a>

                                <!-- About -->
                                <a href="{{ url('/about') }}"
                                    class="nav-item nav-link {{ request()->is('about') ? 'active' : '' }}">
                                    About
                                </a>

                                <!-- Service -->
                                <a href="{{ url('/service') }}"
                                    class="nav-item nav-link {{ request()->is('service') ? 'active' : '' }}">
                                    Service
                                </a>

                                <!-- Team -->
                                <a href="{{ url('/team') }}"
                                    class="nav-item nav-link {{ request()->is('team') ? 'active' : '' }}">
                                    Team
                                </a>

                                <!-- Project -->
                                <a href="{{ url('/portfolio') }}"
                                    class="nav-item nav-link {{ request()->is('portfolio') ? 'active' : '' }}">
                                    Project
                                </a>

                                <!-- Pages -->
                                <div class="nav-item dropdown">

                                    <a href="#"
                                        class="nav-link dropdown-toggle {{ request()->is('blog', 'single') ? 'active' : '' }}"
                                        data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        Pages
                                    </a>

                                    <div class="dropdown-menu">

                                        <a href="{{ url('/blog') }}" class="dropdown-item">
                                            Blog Page
                                        </a>

                                        <a href="{{ url('/single') }}" class="dropdown-item">
                                            Single Page
                                        </a>

                                    </div>

                                </div>

                                <!-- Contact -->
                                <a href="{{ url('/contact') }}"
                                    class="nav-item nav-link {{ request()->is('contact') ? 'active' : '' }}">
                                    Contact
                                </a>

                            </div>

                            <!-- Login Desktop -->
                            <div class="ml-auto mt-2 mt-lg-0 d-none d-lg-block">

                                @auth

                                    @if (auth()->user()->role === 'hrd')
                                        <a class="login-btn" href="{{ url('/hrd/dashboard') }}" title="HRD Dashboard">

                                            <i class="fas fa-user-circle"></i>
                                            <span>Login</span>

                                        </a>
                                    @elseif (auth()->user()->role === 'employee')
                                        <a class="login-btn" href="{{ url('/employee/dashboard') }}" title="Employee Dashboard">

                                            <i class="fas fa-user-circle"></i>
                                            <span>Login</span>

                                        </a>
                                    @else
                                        <a class="login-btn" href="{{ url('/') }}" title="Home">

                                            <i class="fas fa-user-circle"></i>
                                            <span>Login</span>

                                        </a>
                                    @endif
                                @else
                                    <a class="login-btn" href="{{ url('/login') }}" title="Login">

                                        <i class="fas fa-sign-in-alt"></i>
                                        <span>Login</span>

                                    </a>

                                @endauth

                            </div>

                        </div>

                    </nav>

                </div>

            </div>

        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

        <!-- Bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>