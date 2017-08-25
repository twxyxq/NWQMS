@extends('layouts.only_panel')

@section('panel-title')
    <span class="glyphicon glyphicon-home"></span> {!!$current_nav!!}
@endsection

@section('panel-body')
    错误提示：您访问的页面不存在
@endsection

