<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">  
    <link href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/common.css" rel="stylesheet">
    @stack('style')

    <!-- flavr -->
    <link rel="stylesheet" type="text/css" href="/flavr/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/flavr/css/animate.css" />
    <link rel="stylesheet" type="text/css" href="/flavr/css/flavr.css" />

    <!--datapicker-->
    <link href="/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <input type="hidden" id="_token" value="{{ csrf_token() }}">        
    @yield('topbar')
    <div id="app">
        @yield('sidebar')
        <div>
            @yield('content')
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/app.js"></script>
    <script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="/js/function.js"></script>
    <script src="/js/intelligent_input.js"></script>
    <script type="text/javascript" src="/flavr/js/flavr.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script src="/js/common.js"></script>
    <script type="text/javascript">
        $("#module").html($("#module_title a[href='{{ url()->current() }}']").html());
        $("#module_title a[href='{{ url()->current() }}']").remove();
        //alert(jQuery.fn.jquery);
    </script>
    @stack('scripts')
    <!--addition_script-->
</body>
</html>
