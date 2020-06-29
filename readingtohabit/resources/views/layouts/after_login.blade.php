<html>
    <head>
        <title>@yield('title')</title>
        <!-- todo .jsを追加-->
        <!-- todo .cssを追加-->
        <link rel="stylesheet" type="text/css" href="{{DocumentRootConst::DOCUMENT_ROOT}}css/202006301.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
    </head>
    <body>
        @yield('content')

        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}js/202006301.js" defer></script>
        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}js/202006302.js" defer></script>
    </body>
</html>
