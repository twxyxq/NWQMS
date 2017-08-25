@extends('layouts.statistic')

@section('statistic_para')
	<label class="control-label col-sm-1">开始日期：</label>
    <div class="col-sm-3">
        <input type="text" class="form_date form-control input-sm" id="start" data-date-format="yyyy-mm-dd" name="sts_start" readonly="true"  />
    </div>
    <label class="control-label col-sm-1">结束日期：</label>
    <div class="col-sm-3">
        <input type="text" class="form_date form-control input-sm" id="end" data-date-format="yyyy-mm-dd" name="sts_end" readonly="true"  />
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
@endsection

@define $title = $item_name."一次合格率"

@define $yAxis = array("format" => "%", "min" => "generate_min()")

@define $sdata = array(array("title" => "一次合格率", "name" => "rate", "type" => "line", "format" => "%"));

@push('scripts')
<script type="text/javascript">
    function generate_title(){
        return $("[name='sts_start']").val()+"-"+$("[name='sts_end']").val()+"焊工一次合格率";
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