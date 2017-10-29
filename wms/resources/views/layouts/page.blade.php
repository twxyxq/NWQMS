@extends('layouts.app')

@section('topbar')
    <style type="text/css">
        #app > div{
            padding-top: 65px;
        }
    </style>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                @if(isset($current_module))
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    <li class="dropdown" style="display: inline-block;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="display: inline-block;">
                            <div id="module_button"><strong><span id="module">{{$current_module}}</span></strong> <span class="caret"></span></div>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li id="module_title">
                                <!--module-->
                                @foreach($module as $m)
                                    <a href='\{{$m[0]}}'><span class='{{$m[2]==""?"glyphicon glyphicon-th":$m[2]}}'></span> &nbsp; {{$m[1]}}</a>
                                @endforeach
                            </li>
                        </ul>
                    </li>
                </ul>
                @endif
            </div>
            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                @if(isset($top_nav))
                <ul class="nav navbar-nav navbar-left" style="padding: 0 10px;">
                    <!--top_nav-->
                    @foreach($top_nav as $tn)
                        @if(sizeof($tn[2]) > 0)
                        <li id='{{$tn[0]}}' class='dropdown''><a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-expanded='false'>{{$tn[1]}}</a>
                            <ul class='dropdown-menu' role='menu'>
                            <!--dp_#/#0#/#-->
                                @foreach($tn[2] as $tn_item)
                                    <li id='{{str_replace("=","-",str_replace("?","-",$tn_item[0]))}}'><a href='/{{$tn_item[0]}}'><span class='{{$tn_item[2]==""?"glyphicon glyphicon-th":$tn_item[2]}}'></span> &nbsp; {{$tn_item[1]}}</a></li>
                                @endforeach
                            </ul>
                        </li>
                        @else
                            <li id='{{str_replace("=","-",str_replace("?","-",$tn[0]))}}'><a href='/{{$tn[0]}}'>{{$tn[1]}}</a></li>
                        @endif
                    @endforeach
                </ul>
                @endif
                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">登录</a></li>
                        <li><a href="{{ url('/register') }}">注册</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <span class="glyphicon glyphicon-user"></span> {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/home') }}">个人工作台</a>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        注销
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
@endsection
