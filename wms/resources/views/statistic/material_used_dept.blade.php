@extends('layouts.statistic')

@section('statistic_para')
    <label class="control-label col-sm-1">周期：</label>
    <div class="col-sm-3">
        <select name="period" class="form-control input-sm">
            <option value="week">周统计</option>
            <option value="month">月统计</option>
        </select>
    </div>
    <label class="control-label col-sm-1">机组：</label>
    <div class="col-sm-3">
        <select name="ild" class="form-control input-sm">
            <option value="-1">全部</option>
            @foreach(\App\wj::select("ild")->groupby("ild")->get() as $wj)
                <option value="{{$wj->ild}}">{{$wj->ild}}</option>
            @endforeach
        </select>
    </div>
    <label class="control-label col-sm-1">系统：</label>
    <div class="col-sm-3">
        <input name="sys" class="form-control input-sm" bind="{model:'wj',col:'sys'}">
    </div>
@endsection

@define $title = "焊材用量"

@define $sdata = array(array("title" => "热机焊条", "name" => "rj_rod", "type" => "bar", "stack" => "焊条"),array("title" => "热机焊丝", "name" => "rj_wire", "type" => "bar", "stack" => "焊丝"),array("title" => "机械化焊条", "name" => "jxh_rod", "type" => "bar", "stack" => "焊条"),array("title" => "机械化焊丝", "name" => "jxh_wire", "type" => "bar", "stack" => "焊丝"),array("title" => "电仪焊条", "name" => "dy_rod", "type" => "bar", "stack" => "焊条"),array("title" => "电仪焊丝", "name" => "dy_wire", "type" => "bar", "stack" => "焊丝"));

@push('scripts')
<script type="text/javascript">
    function generate_title(){
        return ($("[name='ild']").val()==-1?"":($("[name='ild']").val()+"号机组"))+($("[name='sys']").val()==-1?"":$("[name='sys']").val())+"焊材用量"+$("[name='period']").find("option:selected").text();
    }
</script>
@endpush