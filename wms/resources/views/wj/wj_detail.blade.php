@extends('layouts.app')

<style type="text/css">

</style>

@section('content')

    @define $wj = \App\wj::select(array(DB::raw("*"),DB::raw(SQL_VCODE." as wj_code"),DB::raw(SQL_EXAM_RATE." as rate"),DB::raw(SQL_BASE_TYPE." as type")))->where("id",$id)->get()[0];
    @define $wj_model = new \App\wj()
    @if($wj->tsk_id > 0)
        @define $tsk = \App\tsk::find($wj->tsk_id);
        @define $wjs = \App\wj::select(array(DB::raw("*"),DB::raw(SQL_VCODE." as wj_code"),DB::raw(SQL_EXAM_RATE." as rate"),DB::raw(SQL_BASE_TYPE." as type")))->where("tsk_id",$tsk->id)->get();
        @define $wps = \App\wps::withoutGlobalScopes()->find($tsk->wps_id)
        @define $qp = \App\qp::withoutGlobalScopes()->find($tsk->qp_id)
        @define $material = \App\material_sheet::where("ms_tsk_ids","LIKE","%{".$tsk->id."}%")->get();
    @endif

    <div class="container">
        <div>
            <ul id="tskTabs" class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#info" id="info-tab" role="tab" data-toggle="tab" aria-controls="home" aria-expanded="false">焊口信息</a></li>
                <li role="presentation" class=""><a href="#tsk" role="tab" id="tsk-tab" data-toggle="tab" aria-controls="profile" aria-expanded="true">任务信息</a></li>
                <li role="presentation" class=""><a href="#exam" role="tab" id="exam-tab" data-toggle="tab" aria-controls="profile" aria-expanded="true">检验信息</a></li>
                <li role="presentation" class=""><a href="#sheet" role="tab" id="sheet-tab" data-toggle="tab" aria-controls="profile" aria-expanded="true">记录单</a></li>
            </ul>
            <div id="tskTabContent" class="tab-content">
                <div role="tabpanel" class="tab-pane fade active in" id="info" aria-labelledby="info-tab">
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
                </div>
                <div role="tabpanel" class="tab-pane fade" id="tsk" aria-labelledby="tsk-tab">
                    @if(isset($tsk))
                    <div class="col-sm-12">
                        <span class="glyphicon glyphicon-info-sign"></span> 任务信息
                    </div>

                    <div class="col-sm-4">
                        <strong>《{{$tsk->tsk_title}}》</strong>
                    </div>
                    <div class="col-sm-4">
                        日期：{{$tsk->tsk_date}}
                    </div>
                    <div class="col-sm-4">
                        {{$tsk->created_at}}
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
                    <div class="col-sm-12">
                        <span class="glyphicon glyphicon-info-sign"></span> 焊材信息
                    </div>

                    @foreach($material as $m)
                    <div class="col-sm-12">
                        <div class="col-sm-5">
                            <strong>{{"[".$m->ms_m_type."]".$m->ms_type."φ".$m->ms_diameter." ".$m->ms_s_show}}</strong>
                        </div>
                        <div class="col-sm-4">
                            焊工：{{$m->ms_pp_show}}
                        </div>
                        <div class="col-sm-3">
                            {{strlen($m->ms_time)>0?$m->ms_time:"尚未领取"}}
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
                <div role="tabpanel" class="tab-pane fade" id="exam" aria-labelledby="exam-tab">
                    <div class="col-sm-12" style="padding: 10px">
                        <span class="glyphicon glyphicon-info-sign"></span> 检验信息
                    </div>
                    @define $exam_method = array("RT","UT","PT","MT","SA","HB")
                    @foreach($exam_method as $e)
                        <div class="col-sm-12" style="border: 1px solid #EFEFEF">
                            <div class="col-sm-8">
                                <strong>※ {{$e}}（{{$wj->$e}}%）：</strong>
                            </div>
                            <div class="col-sm-4">
                                @define $weight_col = $e."_weight"
                                <strong>检验权重：{{$wj->$weight_col}}%（{!!$wj->$weight_col>=$wj->$e?"满足":"<span style=\"color:red\">不满足</span>"!!}）</strong>
                            </div>
                            @define $plan_col = $e."_plan"
                            @define $group = \App\exam_plan::whereIn("id",multiple_to_array($wj->$plan_col))->get()
                            @foreach($group as $g)
                                <div class="col-sm-11 col-sm-offset-1">
                                    ▶ <a href="###" onclick="new_flavr('/consignation/group_detail?id={{$g->id}}')">{{$g->ep_code}}</a>
                                    @if(strpos($g->ep_wj_samples,"{".$wj->id."}") !== false)
                                        【一次抽选】
                                    @elseif(strpos($g->ep_wj_addition_samples,"{".$wj->id."}") !== false)
                                        【二次抽选】
                                    @elseif(strpos($g->ep_wj_another_samples,"{".$wj->id."}") !== false)
                                        【三次抽选】
                                    @else
                                        【未抽选】
                                    @endif
                                </div>
                                @define $exams = \App\exam::leftJoin("exam_report","exam_report.id","exam.exam_report_id")->where("exam_wj_id",$wj->id)->where("exam_plan_id",$g->id)->get()
                                @foreach($exams as $exam)
                                    <div class="col-sm-10  col-sm-offset-1">
                                        &nbsp; 检验录入：{{$exam->exam_input_time}} &nbsp; 结果：{{$exam->exam_conclusion}}  &nbsp; 报告：
                                        @if($exam->exam_report_id > 0)
                                            报告未出
                                        @else
                                            <a href="###" onclick="new_flavr('/exam/report_detail?report_id={{$exam->exam_report_id}}')">{{$exam->exam_report_code}}</a>
                                        @endif
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <div role="tabpanel" class="tab-pane fade" id="sheet" aria-labelledby="sheet-tab">
                    <div style="display:inline-block; overflow: hidden;">
                        @if(isset($tsk))
                            <div style="text-align: center;">
                                <button class="btn btn-info btn-small" onclick="print_object($('#tsk_{{$tsk->id}}'))">打印</button>
                            </div>
                            {!!view("sheet/tsk_record",["tsk" => $tsk])!!}
                        @else
                            暂未分配任务
                        @endif
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
