@extends('layouts.app')

<style type="text/css">
    table td > span[title] {
        background-color: pink;
    }
</style>

@section('content')
    @include('conn/datatables')
    <div style="text-align: center;">
        @if(DB::table("wj_base")->select("check_p")->where("deleted_at","2037-12-31")->where("title",$_GET['group'])->get()[0]["check_p"] == 0)
            <button id="wj_base_submit" class="btn btn-success btn-small" onclick="wj_check_submit();">提交审核流程</button>
        @else
            <span class="btn btn-default btn-small disabled">本组数据已确认</span>
        @endif
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    function wj_check_submit(){
        $("#wj_base_submit").attr("disabled",true);
        ajax_post("/wj/wj_base_submit",{"group" : "{{$_GET['group']}}"},function(data){
            if (data.suc == 1) {
                $("#wj_base_submit").remove();
                alert_flavr("提交成功",function(){
                    table_flavr('/console/view_procedure?proc=status_avail_procedure&proc_id='+data.proc_id,"焊口生效流程",{
                        info    : {
                            style   : 'Primary',
                            text    : '焊口详情',
                            action  : function(){
                                $('#current_iframe').attr('src','/console/dt_edit?model=wj&id='+data.ids);
                                return false;
                            }
                        },
                        pass    : {
                            style   : 'success',
                            text    : '审批',
                            action  : function(){
                                $('#current_iframe').attr('src','/console/view_procedure?proc=status_avail_procedure&proc_id='+data.proc_id);
                                return false;
                            }
                        },
                        close   : {
                            text    : '关闭'
                        }
                    });
                });
            } if (data.suc == -1){
                $("#example").DataTable().order([0,"asc"]).draw();
                alert_flavr(data.msg);
                $("#wj_base_submit").attr("disabled",false);
            } else if (data.suc == -2){
                $("#example").DataTable().order([1,"desc"]).draw();
                alert_flavr(data.msg);
                $("#wj_base_submit").attr("disabled",false);
            } else {
                alert_flavr(data.msg);
                $("#wj_base_submit").attr("disabled",false);
            }
        });
    }
</script>
@endpush
