@extends('layouts.dash')

@section('content')
    <div class="contact wow fadeInUp">
        <div class="container">
            <div class="section-header text-center">
                <p>Get In Touch</p>
                <h2>For Any Query</h2>
            </div>

            <!-- Google Maps -->
            <div class="contact-map mb-5">
                <iframe
                    src="https://www.google.com/maps?q=Ruko%20Amara%20Residence%20A-15%2C%20Jl.%20Abdul%20Wahab%2C%20Cinangka%2C%20Sawangan%2C%20Depok%2C%20Jawa%20Barat&output=embed"
                    width="100%" height="400" style="border:0; border-radius:5px;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="flaticon-address"></i>
                            <div class="contact-text">
                                <h2>Location</h2>
                                <p>
                                    Ruko Amara Residence No. A-15
                                    Jl. Abdul Wahab, Cinangka,
                                    Kec. Sawangan, Kota Depok,
                                    Jawa Barat 16516
                                </p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="flaticon-call"></i>
                            <div class="contact-text">
                                <h2>Phone</h2>
                                <p>0213889170</p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <i class="flaticon-send-mail"></i>
                            <div class="contact-text">
                                <h2>Email</h2>
                                <p>megantaracipta.b@gmail.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="contact-form">
                        <div id="success"></div>

                        <form name="sentMessage" id="contactForm" novalidate="novalidate">
                            <div class="control-group">
                                <input type="text" class="form-control" id="name" placeholder="Your Name"
                                    required="required" data-validation-required-message="Please enter your name" />
                                <p class="help-block text-danger"></p>
                            </div>

                            <div class="control-group">
                                <input type="email" class="form-control" id="email" placeholder="Your Email"
                                    required="required" data-validation-required-message="Please enter your email" />
                                <p class="help-block text-danger"></p>
                            </div>

                            <div class="control-group">
                                <input type="text" class="form-control" id="subject" placeholder="Subject"
                                    required="required" data-validation-required-message="Please enter a subject" />
                                <p class="help-block text-danger"></p>
                            </div>

                            <div class="control-group">
                                <textarea class="form-control" id="message" placeholder="Message" required="required"
                                    data-validation-required-message="Please enter your message"></textarea>
                                <p class="help-block text-danger"></p>
                            </div>

                            <div>
                                <button class="btn" type="submit" id="sendMessageButton">
                                    Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection