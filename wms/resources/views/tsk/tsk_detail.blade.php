@extends('layouts.app')

<style type="text/css">

</style>

@section('content')
<div class="container">
    <div>
        <ul id="tskTabs" class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#info" id="info-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="false">任务信息</a></li>
            @if(sizeof($wjs) <= 1)
                <li role="presentation" class=""><a href="#wj" role="tab" id="wj-tab" data-toggle="tab" aria-controls="profile" aria-expanded="true">{{$wjs[0]->vcode}}</a></li>
            @else
                <li role="presentation" class="dropdown">
                    <a href="#" id="tskTabDrop_wj" class="dropdown-toggle" data-toggle="dropdown" aria-controls="tskTabDrop_wj_contents" aria-expanded="false">焊口详情 <span class="caret"></span></a>
                    <ul class="dropdown-menu" aria-labelledby="tskTabDrop_wj" id="tskTabDrop_wj_contents">
                        @foreach($wjs as $wj)
                            <li><a href="#wj_{{$wj->id}}" role="tab" id="wj_{{$wj->id}}-tab" data-toggle="tab" aria-controls="wj_{{$wj->id}}">{{$wj->vcode}}</a></li>
                        @endforeach
                    </ul>
                </li>
            @endif
            <li role="presentation" class=""><a href="#sheet" role="tab" id="sheet-tab" data-toggle="tab" aria-controls="profile" aria-expanded="true">记录单</a></li>
        </ul>
        <div id="tskTabContent" class="tab-content">
            <div role="tabpanel" class="tab-pane fade active in" id="info" aria-labelledby="info-tab">
                <div class="col-sm-12">
                    <span class="glyphicon glyphicon-info-sign"></span> 任务信息
                </div>

                <div class="col-sm-4">
                    <strong>《{{$data->tsk_title}}》</strong>
                </div>
                <div class="col-sm-4">
                    日期：{{$data->tsk_date}}
                </div>
                <div class="col-sm-4">
                    {{$data->created_at}}
                </div>


                <div class="col-sm-12">
                    <span class="glyphicon glyphicon-info-sign"></span> 焊口信息
                </div>

                @foreach($wjs as $wj)
                    <div class="col-sm-4">
                        <strong>◇ {{$wj->wj_code}}</strong>
                    </div>
                    <div class="col-sm-4">
                        规格：{{$wj->type}}
                    </div>
                    <div class="col-sm-4">
                        检验：{{$wj->rate}}
                    </div>
                @endforeach

                <div class="col-sm-12">
                    <span class="glyphicon glyphicon-info-sign"></span> 工艺信息
                </div>

                <div class="col-sm-4">
                    <strong>◇ {{$wps->wps_code}}({{$wps->version}})</strong>
                </div>
                <div class="col-sm-4">
                    焊接方法：{{$wps->wps_method}}
                </div>
                <div class="col-sm-4">
                    焊材：{{$wps->wps_wire}} {{$wps->wps_rod}}
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="wj" aria-labelledby="wj-tab">
                @foreach($wjs as $wj)
                    <div class="col-sm-12" style="padding: 10px">
                        <span class="glyphicon glyphicon-info-sign"></span> 基础信息
                    </div>
                    @foreach($wj_model->item as $key => $item)
                        @if($item->input == "init")
                            <div class="col-sm-4" style="padding: 0 3px">
                                <span class="form-control transparent-input restrict-show">◇{{$item->name}}：{{$wj->$key}}</span>
                            </div>
                        @endif
                    @endforeach
                    <div class="col-sm-12" style="padding: 10px">
                        <span class="glyphicon glyphicon-info-sign"></span> 施工信息
                    </div>
                    <div class="col-sm-12" style="padding: 10px">
                        <span class="glyphicon glyphicon-info-sign"></span> 检验信息
                    </div>
                @endforeach
            </div>
            <div role="tabpanel" class="tab-pane fade" id="sheet" aria-labelledby="sheet-tab">
                <div style="display:inline-block; overflow: hidden;">
                    @define echo $sheet;
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style type="text/css">
    $("#tskTabs a").click(function (e) {
        e.preventDefault()
        $(this).tab('show')
    });
</style>
@endpush
