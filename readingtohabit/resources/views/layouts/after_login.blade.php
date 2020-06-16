<html>
    <head>
        <title>@yield('title')</title>
        <!-- todo .jsを追加-->
        <!-- todo .cssを追加-->
        <link rel="stylesheet" type="text/css" href="{{\DocumentRootConst::DOCUMENT_ROOT}}css/readingtohabit.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
    </head>
    <body>
        @yield('content')

        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}js/jquery.min.js" defer></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.js" defer></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/axios@0.18.0/dist/axios.min.js" defer></script>
        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}js/ofi.min.js" defer></script>
        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}/js/readingtohabit.js" defer></script>
    </body>
</html>
