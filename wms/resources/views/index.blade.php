@extends('layouts.page')

@push('style')
<style type="text/css">
body {
    margin-left: 0px;
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
    overflow:hidden;
    background:#016aa9 url(images/bg-n.png) no-repeat center top 60px;
}
.STYLE1 {
    color: #000000;
    font-size: 12px;
}
#container{
    width:800px;
    height:300px;
    display:table;
    margin-top:-150px;
    margin-left:-400px;
    position:absolute;
    left:50%;
    top:45%;
    background-color:rgba(255,255,255,.3);
    padding:10px;
    border-radius:5px;
}
#title-bar{
    display:none;
}
.photo-wall{
    position:relative;
    display:table-cell;
    width:560px;
    height:100%;
    opacity:.8;
}
.divide-area{
    display:table-cell;
    width:10px;
}
.login-area{
    display:table-cell;
    width:230px;
    height:100%;
    background-color:white;
    border:1px solid lightgray;
    border-radius:12px;
    opacity:0.8;
    box-shadow: 3px 3px 12px rgba(0,0,0,0.6);
    padding:15px 20px;
}
.area-title{
    width:100%;
    height:45px;
    display:block;
    border-bottom:1px solid lightgray;
    text-align:left;
    line-height:45px;
    font-size:18px;
}
.text-input{
    position: relative;
    display:block;
    margin-top:22px;
    width:100%;
    align:center;
}
.text-input input{
    width:100%;
    height:30px;
    padding:4px;
    text-align:center;
    border-radius:7px;
}
.text-input button{
    width:100%;
}
.help-block{
    position: absolute;
    top: -26px;
}
#p1,#p2,#p3,#p4{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%
}
#p1{
    background:url(images/p1.jpg) no-repeat left top -110px;
    -webkit-clip-path: polygon(2% 0%, 66.5% 64.4%, 81.8% 0%);
    clip-path: polygon(0% 0%, 66.5% 64.4%, 82.8% 0%);
    z-index:1001;
}
#p2{
    background:url(images/p2.jpg) no-repeat left -160px top;
    -webkit-clip-path: polygon(0% 0%, 0% 100%, 17.2% 100%, 32.9% 32.9%);
    clip-path: polygon(0% 0%, 0% 100%, 17.2% 100%, 32.9% 32.9%);
    z-index:1002;
}
#p3{
    background:url(images/p3.jpg) no-repeat right bottom -160px;
    -webkit-clip-path: polygon(34% 34%, 18.6% 100%, 100% 100%);
    clip-path: polygon(34% 34%, 18.6% 100%, 100% 100%);
    z-index:1000;
}
#p4{
    background:url(images/p4.jpg) no-repeat right -120px top -40px;
    -webkit-clip-path: polygon(82.8% 0%, 67.5% 65.4%, 100% 98%, 100% 0%);
    clip-path: polygon(82.8% 0%, 67.5% 65.4%, 100% 98%, 100% 0%);
    z-index:1003;
}
#web-title{
    width:290px;
    height:50px;
    font-size:26px;
    position:absolute;
    top:20px;
    left:0;
    background-color:rgba(255,0,255,.5);
    line-height:50px;
    z-index:1005;
    text-align:center;
    color:white;
    border-radius:0 8px 8px 0;
    text-shadow:1px 1px rgba(0,0,0,.3);
}
#version-show{
    position:absolute;
    width:340px;
    height:50px;
    bottom:-50px;
    left:50%;
    margin-left:-170px;
    margin-bottom:-25px;
    text-align:center;
    font-size:12px;
    color: white;
}
@media (max-width:800px){
    #container{
        width:250px;
        margin-left:-125px;
        top:52%;
    }
    #title-bar{
        display:block;
    }
    .photo-wall,.divide-area{
        display:none;
    }
}
</style>
<script type="text/javascript" charset="utf-8">

function log_submit(){
    $("#log").submit();
    //alert(1);
}

function log_in(){
    if(event.keyCode == 13){
        $("#log").submit();
    }
    //alert(1);
}
</script>
@endpush

@section('content')

@if (!Auth::guest())
<script type="text/javascript">
    location.href = "/home";
</script>
@endif

<div id="container">
    <div class="photo-wall">
        <div id="web-title"><strong>NWQMS管理平台</strong></div>
        <div id="p1"></div>
        <div id="p2"></div>
        <div id="p3"></div>
        <div id="p4"></div>
    </div>
    <div class="divide-area"></div>
    <div class="login-area">
        <form id="log" method="POST" action="/login"> 
            {{ csrf_field() }}
            <div class="area-title">
                <span class="glyphicon glyphicon-user"></span> &nbsp; <strong>用户登录</strong>
            </div>
            <div class="login-input">
                <span class="text-input">
                    <input type="text" id="code" name="code" placeholder="用户名">
                    @if ($errors->has('code'))
                        <span class="help-block">
                            <strong>用户名或密码错误</strong>
                        </span>
                    @endif
                </span>
                <span class="text-input">
                    <input type="password" id="password" name="password" placeholder="密码">
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>用户名或密码错误</strong>
                        </span>
                    @endif
                </span>
                <span>
                    <input type="checkbox" name="remember"> 记住我  &nbsp;  
                </span>
                <span class="text-input"><button class="btn btn-info">登录</button></span>
                <span><a class="btn btn-link" href="{{ url('/password/reset') }}">忘记密码?</a></span>
            </div>
        </form>
    </div>
    <div id="version-show">
        <strong>Version:V3.00 Edition. Last Update:2017-11-16.</strong>
    </div>
</div>
@endsection