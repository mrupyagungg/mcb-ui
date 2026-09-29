@extends('layouts.dash')

@section('content')

    <!-- About Start -->
    <div class="about wow fadeInUp" data-wow-delay="0.1s">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 col-md-6">
                    <div class="about-img">
                        <img src="img/about.jpg" alt="Image">
                    </div>
                </div>
                <div class="col-lg-7 col-md-6">
                    <div class="section-header text-left">
                        <p>Welcome to PT Megantara Cipta Bersaudara</p>
                        <h2>What is MCB?</h2>
                    </div>
                    <div class="about-text">
                        <p>
                            PT Megantara Cipta Bersaudara (MCB) adalah perusahaan berbadan hukum Perseroan Terbatas
                            (PT) yang didirikan pada tahun 2024 dan bergerak di berbagai sektor usaha strategis.
                            Perusahaan ini menjalankan kegiatan usaha di bidang instalasi listrik, instalasi
                            telekomunikasi, perdagangan peralatan telekomunikasi, layanan telekomunikasi, jasa
                            konsumsi, serta penyelenggaraan acara khusus (special event). Dengan cakupan usaha yang
                            luas, MCB berkomitmen untuk menjadi perusahaan yang adaptif, profesional, dan
                            berkelanjutan dalam memberikan layanan berkualitas serta solusi inovatif bagi klien dan
                            mitra kerja.

                        </p>
                        <a class="btn" href="about.html">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About End -->


    <!-- Fact Start -->
    <div class="fact">
        <div class="container-fluid">
            <div class="row counters">
                <div class="col-md-6 fact-left wow slideInLeft">
                    <div class="row">
                        <div class="col-6">
                            <div class="fact-icon">
                                <i class="flaticon-worker"></i>
                            </div>
                            <div class="fact-text">
                                <h2 data-toggle="counter-up">109</h2>
                                <p>Expert Workers</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="fact-icon">
                                <i class="flaticon-building"></i>
                            </div>
                            <div class="fact-text">
                                <h2 data-toggle="counter-up">485</h2>
                                <p>Happy Clients</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 fact-right wow slideInRight">
                    <div class="row">
                        <div class="col-6">
                            <div class="fact-icon">
                                <i class="flaticon-address"></i>
                            </div>
                            <div class="fact-text">
                                <h2 data-toggle="counter-up">789</h2>
                                <p>Completed Projects</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="fact-icon">
                                <i class="flaticon-crane"></i>
                            </div>
                            <div class="fact-text">
                                <h2 data-toggle="counter-up">890</h2>
                                <p>Running Projects</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact End -->

    <!-- FAQs Start -->
    <div class="faqs" id="faq">
        <div class="container">
            <div class="section-header text-center">
                <p>Pertanyaan yang Sering Diajukan</p>
                <h2>Frequently Asked Questions (FAQ)</h2>
            </div>

            <div class="row">

                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <div id="accordion-1">

                        <div class="card wow fadeInLeft" data-wow-delay="0.1s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqOne">
                                    Apa saja bidang usaha PT Megantara Cipta Bersaudara?
                                </a>
                            </div>
                            <div id="faqOne" class="collapse" data-parent="#accordion-1">
                                <div class="card-body">
                                    PT Megantara Cipta Bersaudara bergerak di bidang instalasi listrik,
                                    instalasi telekomunikasi, perdagangan peralatan telekomunikasi,
                                    layanan telekomunikasi, layanan event, dan layanan konsumsi.
                                </div>
                            </div>
                        </div>

                        <div class="card wow fadeInLeft" data-wow-delay="0.2s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqTwo">
                                    Apakah MCB menerima proyek di seluruh Indonesia?
                                </a>
                            </div>
                            <div id="faqTwo" class="collapse" data-parent="#accordion-1">
                                <div class="card-body">
                                    Ya. Kami siap melayani kebutuhan proyek di berbagai wilayah Indonesia
                                    sesuai dengan ruang lingkup pekerjaan dan kebutuhan pelanggan.
                                </div>
                            </div>
                        </div>

                        <div class="card wow fadeInLeft" data-wow-delay="0.3s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqThree">
                                    Siapa saja yang dapat menggunakan layanan MCB?
                                </a>
                            </div>
                            <div id="faqThree" class="collapse" data-parent="#accordion-1">
                                <div class="card-body">
                                    Layanan kami ditujukan untuk perusahaan, instansi pemerintah,
                                    BUMN, swasta, maupun individu yang membutuhkan layanan profesional
                                    sesuai bidang usaha kami.
                                </div>
                            </div>
                        </div>

                        <div class="card wow fadeInLeft" data-wow-delay="0.4s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqFour">
                                    Apakah MCB dapat menangani proyek skala besar?
                                </a>
                            </div>
                            <div id="faqFour" class="collapse" data-parent="#accordion-1">
                                <div class="card-body">
                                    Ya. Kami memiliki komitmen untuk menangani proyek mulai dari skala
                                    kecil hingga besar dengan mengutamakan kualitas, keselamatan,
                                    dan ketepatan waktu.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <div id="accordion-2">

                        <div class="card wow fadeInRight" data-wow-delay="0.1s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqFive">
                                    Bagaimana cara mengajukan penawaran kerja sama?
                                </a>
                            </div>
                            <div id="faqFive" class="collapse" data-parent="#accordion-2">
                                <div class="card-body">
                                    Anda dapat menghubungi kami melalui halaman kontak, email,
                                    maupun nomor telepon yang tersedia. Tim kami akan segera
                                    menindaklanjuti kebutuhan Anda.
                                </div>
                            </div>
                        </div>

                        <div class="card wow fadeInRight" data-wow-delay="0.2s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqSix">
                                    Apakah MCB memberikan konsultasi sebelum proyek dimulai?
                                </a>
                            </div>
                            <div id="faqSix" class="collapse" data-parent="#accordion-2">
                                <div class="card-body">
                                    Ya. Kami menyediakan konsultasi awal untuk memahami kebutuhan
                                    pelanggan sehingga solusi yang diberikan sesuai dengan target proyek.
                                </div>
                            </div>
                        </div>

                        <div class="card wow fadeInRight" data-wow-delay="0.3s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqSeven">
                                    Apa komitmen utama PT Megantara Cipta Bersaudara?
                                </a>
                            </div>
                            <div id="faqSeven" class="collapse" data-parent="#accordion-2">
                                <div class="card-body">
                                    Kami berkomitmen memberikan layanan yang profesional,
                                    berkualitas, inovatif, tepat waktu, serta mengutamakan
                                    kepuasan pelanggan dalam setiap pekerjaan.
                                </div>
                            </div>
                        </div>

                        <div class="card wow fadeInRight" data-wow-delay="0.4s">
                            <div class="card-header">
                                <a class="card-link collapsed" data-toggle="collapse" href="#faqEight">
                                    Bagaimana cara menghubungi PT Megantara Cipta Bersaudara?
                                </a>
                            </div>
                            <div id="faqEight" class="collapse" data-parent="#accordion-2">
                                <div class="card-body">
                                    Anda dapat menghubungi kami melalui email, nomor telepon,
                                    maupun datang langsung ke kantor PT Megantara Cipta Bersaudara
                                    yang berlokasi di Kota Depok, Jawa Barat.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- FAQs End -->

@endsection