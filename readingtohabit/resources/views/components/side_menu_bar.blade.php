<div class="side_menu_bar">
    <div class="menu_head">
        <div class="menu_profile_img_area">
            <div class="menu_profile_img_content">
                <img src="{{session()->get('profile_img')}}?{{session()->get('current_date')}}" class="menu_profile_img object-fit-img" />
            </div>
        </div>
        <div class="menu_add_article_area">
            <a class="to_add_article" href="add_article_search_book">
                <i class="fas fa-pen-square mr_2_inline"></i>記録を書く
            </a>
        </div>
    </div>

    <div class="menu_links">
        @if ($current_page === 'articles')
        <a class="menu_with_icon selected" href="articles">
            <i class="fas fa-list mr_2_inline icon_color_primary_with_text"></i>
            <div class="color_primary_with_icon">記録一覧</div>
        </a>
        @else
        <a class="menu_with_icon not_selected" href="articles">
            <i class="fas fa-list mr_2_inline icon_color_default_with_text"></i>
            <div class="color_default_with_icon">記録一覧</div>
        </a>
        @endif
            
        @if ($current_page === 'search_articles')
        <a class="menu_with_icon selected" href="search_article_form">
            <i class="fas fa-search mr_2_inline icon_color_primary_with_text"></i>
            <div class="color_primary_with_icon">記録検索</div>
        </a>
        @else
        <a class="menu_with_icon not_selected" href="search_article_form">
            <i class="fas fa-search mr_2_inline icon_color_default_with_text"></i>
            <div class="color_default_with_icon">記録検索</div>
        </a>
        @endif
            
        @if ($current_page === 'favorites')
        <a class="menu_with_icon selected" href="favorites">
            <i class="fas fa-star mr_2_inline icon_color_primary_with_text"></i>
            <div class="color_primary_with_icon">お気に入り</div>
        </a>
        @else
        <a class="menu_with_icon not_selected" href="favorites">
            <i class="fas fa-star mr_2_inline icon_color_default_with_text"></i>
            <div class="color_default_with_icon">お気に入り</div>
        </a>
        @endif
        
        @if ($current_page === 'edit_profile')
        <a class="menu selected" href="edit_profile">
            <div class="color_primary">プロフィール編集</div>
        </a>
        @else
        <a class="menu not_selected" href="edit_profile">
            <div class="color_default">プロフィール編集</div>
        </a>
        @endif
        
        @if ($current_page === 'edit_password')
        <a class="menu selected" href="edit_password">
            <div class="color_primary">パスワード編集</div>
        </a>
        @else
        <a class="menu not_selected" href="edit_password">
            <div class="color_default">パスワード編集</div>
        </a>
        @endif
            
        @if ($current_page === 'edit_default_mail_timing')
        <a class="menu selected" href="edit_default_mail_timing">
            <div class="color_primary">デフォルト配信タイミング編集</div>
        </a>
        @else
        <a class="menu not_selected" href="edit_default_mail_timing">
            <div class="color_default">デフォルト配信タイミング編集</div>
        </a>
        @endif
            
        @if ($current_page === 'rules')
        <a class="menu selected" href="rules">
            <div class="color_primary">利用規約</div>
        </a>
        @else
        <a class="menu not_selected" href="rules">
            <div class="color_default">利用規約</div>
        </a>
        @endif
            
        @if ($current_page === 'privacy_policy')
        <a class="menu selected" href="privacy_policy">
            <div class="color_primary">プライバシーポリシー</div>
        </a>
        @else
        <a class="menu not_selected" href="privacy_policy">
            <div class="color_default">プライバシーポリシー</div>
        </a>
        @endif
        <a class="menu_bottom not_selected" href="logout">
            <div class="color_default">ログアウト</div>
        </a>
    </div>
</div>
