@extends('layouts.dash')

@section('content')
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
                            <button class="btn btn-warning" id="btnSearch" type="button">
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
                <h5 class="mt-3">Loading latest infrastructure news...</h5>
                <p class="text-muted">Please wait...</p>
            </div>

            <!-- Empty State -->
            <div id="emptyNews" class="text-center py-5" style="display:none;">
                <i class="fa fa-newspaper fa-4x text-secondary mb-4"></i>
                <h4>No News Found</h4>
                <p class="text-muted">Try another keyword.</p>
            </div>

            <!-- News Container -->
            <div class="row blog-page" id="newsContainer"></div>

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12">
                    <nav>
                        <ul class="pagination justify-content-center" id="pagination"></ul>
                    </nav>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const newsContainer = document.getElementById('newsContainer');
            const loadingNews = document.getElementById('loadingNews');
            const emptyNews = document.getElementById('emptyNews');
            const searchInput = document.getElementById('searchNews');
            const searchButton = document.getElementById('btnSearch');

            let allNews = [];

            function loadNews() {

                loadingNews.style.display = 'block';
                emptyNews.style.display = 'none';

                fetch('/news.php')
                    .then(response => {

                        if (!response.ok) {
                            throw new Error('News request failed');
                        }

                        return response.json();

                    })
                    .then(data => {

                        console.log('NEWS DATA:', data);

                        loadingNews.style.display = 'none';

                        allNews = Array.isArray(data) ? data : [];

                        displayNews(allNews);

                    })
                    .catch(error => {

                        console.error(error);

                        loadingNews.style.display = 'none';

                        newsContainer.innerHTML = '';

                        emptyNews.style.display = 'block';

                        emptyNews.querySelector('h4').textContent =
                            'Unable to Load News';

                        emptyNews.querySelector('p').textContent =
                            'Please try again later.';

                    });
            }

            function displayNews(news) {

                newsContainer.innerHTML = '';

                if (!news.length) {

                    emptyNews.style.display = 'block';

                    return;
                }

                emptyNews.style.display = 'none';

                news.forEach(item => {

                    const title =
                        item.title || 'Untitled News';

                    const description =
                        item.description ||
                        'No description available.';

                    const image =
                        item.image ||
                        '/img/blog-default.jpg';

                    const url =
                        item.url || '#';

                    const date =
                        item.date || '';

                    const category =
                        item.category || 'News';

                    const source =
                        item.source || 'News';

                    const card = `
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="blog-item h-100">

                                <div class="blog-img">
                                    <img
                                        src="${image}"
                                        alt="${escapeHtml(title)}"
                                        class="img-fluid"
                                        style="
                                            width:100%;
                                            height:220px;
                                            object-fit:cover;
                                        "
                                        onerror="
                                            this.src='/img/blog-default.jpg';
                                        ">
                                </div>

                                <div class="blog-text">

                                    <div class="mb-2">
                                        <span class="badge badge-warning">
                                            ${category}
                                        </span>

                                        <span class="text-muted ml-2">
                                            ${source}
                                        </span>
                                    </div>

                                    <h3>
                                        <a
                                            href="${url}"
                                            target="_blank"
                                            rel="noopener">
                                            ${escapeHtml(title)}
                                        </a>
                                    </h3>

                                    <p>
                                        ${escapeHtml(description)}
                                    </p>

                                    <div class="blog-meta">

                                        <span>
                                            <i class="fa fa-calendar"></i>
                                            ${date}
                                        </span>

                                    </div>

                                    <a
                                        href="${url}"
                                        target="_blank"
                                        rel="noopener"
                                        class="btn btn-warning mt-3">
                                        Read More
                                    </a>

                                </div>

                            </div>
                        </div>
                    `;

                    newsContainer.insertAdjacentHTML(
                        'beforeend',
                        card
                    );
                });
            }

            function searchNews() {

                const keyword =
                    searchInput.value
                        .trim()
                        .toLowerCase();

                if (!keyword) {

                    displayNews(allNews);

                    return;
                }

                const filtered = allNews.filter(item => {

                    const title =
                        (item.title || '')
                            .toLowerCase();

                    const description =
                        (item.description || '')
                            .toLowerCase();

                    const category =
                        (item.category || '')
                            .toLowerCase();

                    return (
                        title.includes(keyword) ||
                        description.includes(keyword) ||
                        category.includes(keyword)
                    );
                });

                displayNews(filtered);
            }

            function escapeHtml(text) {

                const div =
                    document.createElement('div');

                div.textContent = text;

                return div.innerHTML;
            }

            searchButton.addEventListener(
                'click',
                searchNews
            );

            searchInput.addEventListener(
                'keyup',
                function (event) {

                    if (event.key === 'Enter') {
                        searchNews();
                    }

                }
            );

            loadNews();

        });
    </script>
@endpush