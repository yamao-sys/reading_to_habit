<div class="footer_nav_area">
    <div class="footer_nav">
        @if ($current_page === 'articles')
        <a class="footer_nav_content selected" href="articles">
            <div class="mb_2"><i class="fas fa-list color_primary"></i></div>
            <div class="char_color_primary">記録一覧</div>
        </a>
        @else
        <a class="footer_nav_content not_selected" href="articles">
            <div class="mb_2"><i class="fas fa-list color_default"></i></div>
            <div class="char_color_default">記録一覧</div>
        </a>
        @endif

        @if ($current_page === 'search_articles')
        <a class="footer_nav_content selected" href="search_article_form">
            <div class="mb_2"><i class="fas fa-search color_primary"></i></div>
            <div class="char_color_primary">記録検索</div>
        </a>
        @else
        <a class="footer_nav_content not_selected" href="search_article_form">
            <div class="mb_2"><i class="fas fa-search color_default"></i></div>
            <div class="char_color_default">記録検索</div>
        </a>
        @endif

        @if ($current_page === 'favorites')
        <a class="footer_nav_content selected" href="favorites">
            <div class="mb_2"><i class="fas fa-star color_primary"></i></div>
            <div class="char_color_primary">お気に入り</div>
        </a>
        @else
        <a class="footer_nav_content not_selected" href="favorites">
            <div class="mb_2"><i class="fas fa-star color_default"></i></div>
            <div class="char_color_default">お気に入り</div>
        </a>
        @endif
    </div>
</div>
