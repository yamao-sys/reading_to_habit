<html>
    <head>
        <title>@yield('title')</title>
        <link rel="stylesheet" type="text/css" href="{{\DocumentRootConst::DOCUMENT_ROOT}}/css/readingtohabit.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Cache-Control" content="no-cache">
    </head>
    <body>
        <div id="wrapper">
            <div class="content_area">
            @yield('content')
            </div>
        </div>
        
        <script type="text/javascript" src="{{\DocumentRootConst::DOCUMENT_ROOT}}js/readingtohabit.js" defer></script>
    </body>
</html>
