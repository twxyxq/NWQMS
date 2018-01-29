<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}{{strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger")?"[".Auth::user()->name."]":""}}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">  
    <link href="/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/common.css" rel="stylesheet">
    @stack('style')

    <!-- flavr -->
    <link rel="stylesheet" type="text/css" href="/flavr/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/flavr/css/animate.css" />
    <link rel="stylesheet" type="text/css" href="/flavr/css/flavr.css" />

    <!--print-->

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
    @if(strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") === false)   
        @yield('topbar')
    @endif
    
    @yield('layout')
    <div id="app">
        @yield('sidebar')
        <div>
            @yield('content')
            {!!isset($app_content)?$app_content:""!!}
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/app.js"></script>
    @stack('pre_scripts')
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/function.js?v=0.3"></script>
    <script src="/js/intelligent_input.js?v=0.1"></script>
    <script src="/js/LodopFuncs.js"></script>
    <script type="text/javascript" src="/flavr/js/flavr.min.js"></script>
    <script type="text/javascript" src="/js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script type="text/javascript" src="/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script src="/js/common.js?v=0.3"></script>
    <script type="text/javascript">
        $("#module").html($("#module_title a[href='{{ url()->current() }}']").html());
        $("#module_title a[href='{{ url()->current() }}']").remove();
    </script>
    @stack('scripts')
    <!--addition_script-->
    {!!isset($addition_script)?$addition_script:""!!}


    @if(Auth::check() && Auth::user()->user_org == "已禁用")
        {{Auth::logout()}}
        <script type="text/javascript">
            alert_flavr("用户已禁用，自动退出",function(){
                location.reload();
            });
        </script>
    @endif


</body>
</html>
