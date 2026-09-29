@extends('layouts.dash')

@section('content')
    <!-- Blog Start -->
    <div class="blog">
        <div class="container">

            <div class="section-header text-center">
                <p>Latest Infrastructure News</p>
                <h2>Infrastructure & Telecommunication News</h2>
            </div>

            <!-- Search -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-6">
                    <div class="input-group">

                        <input type="text" id="searchNews" class="form-control" placeholder="Search infrastructure news...">

                        <div class="input-group-append">
                            <button class="btn btn-warning" id="btnSearch">

                                <i class="fa fa-search"></i>

                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Loading -->
            <div id="loadingNews" class="text-center py-5">

                <div class="spinner-border text-warning" role="status">
                    <span class="sr-only">Loading...</span>
                </div>

                <h5 class="mt-3">
                    Loading latest infrastructure news...
                </h5>

                <p class="text-muted">
                    Please wait...
                </p>

            </div>

            <!-- Empty State -->
            <div id="emptyNews" class="text-center py-5" style="display:none;">

                <i class="fa fa-newspaper fa-4x text-secondary mb-4"></i>

                <h4>No News Found</h4>

                <p class="text-muted">
                    Try another keyword.
                </p>

            </div>

            <!-- News Container -->
            <div class="row blog-page" id="newsContainer">

                <!-- JavaScript akan membuat card berita di sini -->

            </div>

            <!-- Pagination -->
            <div class="row mt-5">

                <div class="col-12">

                    <nav>

                        <ul class="pagination justify-content-center" id="pagination">

                        </ul>

                    </nav>

                </div>

            </div>

        </div>
    </div>
    <!-- Blog End -->

@endsection