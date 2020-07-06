<html>
    <head>
        <title>@yield('title')</title>
        <link rel="stylesheet" type="text/css" href="{{DocumentRootConst::DOCUMENT_ROOT}}css/202006301.css">
        <link rel="shortcut icon" href="{{\DocumentRootConst::DOCUMENT_ROOT}}favicon.ico" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
    </head>
    <body>
        <div id="wrapper">
        <header id="header_wrapper" class="header_before_login">
            <div class="header_before_login_service">
                <div class="service_area">
                    <small class="service">読書記録×習慣化サービス</small>
                </div>
            </div>
            <div class="header_before_login_body">
                <div class="header_body_area">
                    <div class="header_before_login_logo"><a href="top" class="link_dark">Readingtohabit</a></div>
                    <div v-if="smaller === true" class="header_navi">
                        <input id="nav_input" type="checkbox" class="nav_unshown">
                        <label id="nav_open" for="nav_input"><span></span></label>
                        <label class="nav_unshown" id="nav_close" for="nav_input"></label>
                        <div id="nav_content">
                            <div class="mt_2rem mb_2rem">
                                <a href="register_user_form" class="menu_list">ユーザー登録</a>
                            </div>
                            <div class="mt_2rem mb_2rem">
                                <a href="login" class="menu_list">ログイン</a>
                            </div>
                            <div class="mt_2rem mb_2rem">
                                <a href="contact_form" class="menu_list">お問い合わせ</a>
                            </div>
                        </div>
                    </div>
                    <div v-if="larger === true" class="header_menu_large_area">
                        <div class="header_menu_large">
                            <div>
                                <a href="register_user_form" class="menu_list">ユーザー登録</a>
                            </div>
                            <div>
                                <a href="login" class="menu_list">ログイン</a>
                            </div>
                            <div>
                                <a href="contact_form" class="menu_list">お問い合わせ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content_area">
        @yield('content')
        </div>

        <footer class="footer_default">
            <div class="footer_body">
                <div class="footer_area">
                    <a href="rules" class="footer_content">利用規約</a>
                    <a href="privacy_policy" class="footer_content">プライバシーポリシー</a>
                </div>
                <div class="footer_copyright">
                     (C) 2020 KEITA YAMAUCHI
                </div>
            </div>
        </footer>

        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}js/202006301.js" defer></script>
        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}js/202006302.js" defer></script>
        </div>
    </body>
</html>
