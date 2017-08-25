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
@endsection

@define $title = "焊材用量"

@define $sdata = array(array("title" => "准备区使用量", "name" => "pre_amount", "type" => "bar", "stack" => "用量"),array("title" => "现场使用量", "name" => "loc_amount", "type" => "bar", "stack" => "用量"));

@push('scripts')
<script type="text/javascript">
    function generate_title(){
        return $("[name='sts_start']").val()+"-"+$("[name='sts_end']").val()+"焊材用量";
    }
</script>
@endpush