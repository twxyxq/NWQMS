@extends('layouts.statistic')

@section('statistic_para')
	<label class="control-label col-sm-1">周期：</label>
	<div class="col-sm-3">
    	<select name="period" class="form-control input-sm">
    		<option value="week">周统计</option>
    		<option value="month">月统计</option>
            <option value="year">年统计</option>
            <option value="day">日统计</option>
    	</select>
	</div>
    <label class="control-label col-sm-1">方法：</label>
    <div class="col-sm-3">
        <select name="emethod" class="form-control input-sm">
            <option value="">不限</option>
            <option value="RT">RT</option>
            <option value="UT">UT</option>
            <option value="PT">PT</option>
            <option value="MT">MT</option>
            <option value="SA">SA</option>
            <option value="HB">HB</option>
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

@define $title = $item_name."一次合格率"

@define $yAxis = array("format" => "%", "min" => "generate_min()")

@define $sdata = array(array("title" => "一次合格率", "name" => "rate", "type" => "line", "format" => "%"));

@push('scripts')
<script type="text/javascript">
    function generate_title(){
        return ($("[name='ild']").val()==-1?"":($("[name='ild']").val()+"号机组"))+($("[name='sys']").val()==""?"":$("[name='sys']").val())+($("[name='emethod']").val()==""?"":$("[name='emethod']").val())+"{{$item_name}}一次合格率"+$("[name='period']").find("option:selected").text();
    }
    function generate_min(){
        var min_value = 100;
        $("span[id^='rate']").each(function(){
            if(Number($(this).html()) < min_value){
                min_value = Number($(this).html());
            }
        });
        min_value =  min_value-(100-min_value)*0.3;
        return min_value.toFixed(1);
    }
</script>
@endpush