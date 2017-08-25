@extends('layouts.page')

@push("style")
<style type="text/css">
    #input-div {
        overflow: auto;
    }
    #input-data {
        width:2000px;
    }
    #input-data td{
        border: 1px solid lightgray;
        width:40px;
        max-width:40px;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: center;
        white-space: nowrap;
    }
    #input-data tr:first-child{
        background-color: #EAE8FA;
    }
    #input-data tr[isvalue='0']{
        background-color: rgba(200,200,200,0.25);
    }
    #input-data tr[isvalue='0'] + tr[isvalue='0'] + tr[isvalue='0'] + tr[isvalue='0'] + tr[isvalue='0'] + tr[isvalue='0']{
        display: none;
    }
    #input-data tr td:first-child{
        width: 24px;
        max-width: 24px;
        background-color: #EAE8FA;
    }
    #input-data td[value]){
        background-color: white;
    }
    #input-data tr[isvalue='1'] > td[not_null][isvalid='0']{
        background-color: lightyellow;
    }
    #input-data td[isvalid='0']:not([title='']){
        background-color: pink !important;
    }
    #input-data tr.unique > td[unique]{
        border: 2px solid red;
    }
    .td-select{
        border: 2px solid lightgreen !important;
    }
    .td-highlight{
        border: 2px solid #3C73FF !important;
    }
    textarea#a{
        position: absolute;
        left: 0px;
        top: -40px;
        opacity: 0;
        z-index: -9999;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-home"></span> {!!$current_nav!!}
                </div>

                <div class="panel-body">
                    <textarea id="a" cols="1" rows="1" width="1px"></textarea>
                    <button class="btn btn-success btn-small" onclick="table_data_submit()">确认录入</button> &nbsp; 
                    请在表格中录入数据，数据会自动验算，显示为白色的为有效数据，粉色框为格式不正确的数据，最多支持100条数据

                </div>
            </div>
        </div>
    </div>
</div>
<div id="input-div" class="container">
        <table id="input-data" select="0">
            <tr>
                <td></td>
                @foreach($wj->titles_init(array("焊缝级别","指定检验","指定理由","RT","UT","PT","MT","SA","HB")) as $title)
                <td>{{$title}}</td>
                @endforeach
            </tr>
            @for($i = 0; $i < 100; $i++)
            <tr id="row-{{$i}}" isvalue="0" isvalid="0" unique="1">
                <td>{{$i+1}}</td>
                @define $j = 0
                @foreach($wj->items_init(array("level","exam_specify","exam_specify_reason","RT","UT","PT","MT","SA","HB")) as $item)
                <td id="{{$item}}-{{$i}}" name="{{$i}}-{{$j++}}" col="{{$item}}" value="" isvalid="0" valid="" title="" {{$wj->item->$item->def=='null'?'':'not_null=1'}} {{in_array($item,array('ild','sys','pipeline','vnum'))?'unique':''}}></td>
                @endforeach
            </tr>
            @endfor
        </table>
</div>
@endsection

@push('scripts')
<script type="text/javascript">

    function add_input(obj){
        obj.html("<input id=\"input-"+obj.attr('id')+"\" name=\"table-input\" type=\"text\" class=\"transparent-input\" width=\"38px\" onblur=\"blur_valid($(this).attr('id'))\" value=\""+obj.attr("value")+"\">");
    }


    $("#input-data td[value]").on("mousedown",function(){
        $("#input-data").attr("select",1);
        var this_td = $(this);
        if (this_td.hasClass("td-highlight")) {
            if (this_td.find("[name='table-input']").length == 0) {
                add_input(this_td);
            }
        } else {
            $("#input-data td").removeClass("td-highlight");
            $("#input-data td").removeClass("td-select");
            this_td.addClass("td-highlight");
            this_td.addClass("td-select");
            $("[name='table-input']").blur();
            add_input(this_td);
        }
    });
    $("#input-data td[value]").on("mouseover",function(){
        if ($("#input-data").attr("select") == 1 && $(".td-highlight").length == 1) {
            
            var highlight = $(".td-highlight").attr("name").split("-");
            var mouse = $(this).attr("name").split("-");

            $("#input-data td").removeClass("td-select");
            for (var i = Math.min(highlight[0],mouse[0]); i <= Math.max(highlight[0],mouse[0]); i++) {
                for (var j = Math.min(highlight[1],mouse[1]); j <= Math.max(highlight[1],mouse[1]); j++) {
                    $("[name='"+i+"-"+j+"']").addClass("td-select");
                }
            }
            //$(this).addClass("td-select");
        }
    });
    $(document).on("mouseup",function(){
        $("#input-data").attr("select",0);
        if ($("#input-data td.td-select").length > 1) {
            $("#input-data td").removeClass("td-highlight");
            $("[name='table-input']").blur();
        }
    })
    $(document).on("keydown", function(e){
        if ((e.which == 8 || e.which == 46) && !$(".td-highlight input[name='table-input']").is(":focus")) {
            $(".td-select").html("");
            $(".td-select").attr("value","");
            $(".td-select").attr("valid","");
            $(".td-select").attr("isvalid",0);
            $(".td-select").attr("title","");
            var row_static = -1;
            $(".td-select").each(function(){
                var row = $(this).attr("name").split("-")[0];
                if (row != row_static) {
                    check_row_available(row);
                    row_static = row;
                }
            });
        } else {
            if ($(".td-highlight").length == 1) {
                if ($(".td-highlight input[name='table-input']").length == 0){
                    add_input($("td.td-highlight"));
                }
                if(!$(".td-highlight input[name='table-input']").is(":focus")) {
                    $(".td-highlight input[name='table-input']").focus();
                    $(".td-highlight input[name='table-input']").val($(".td-highlight input[name='table-input']").val());
                }
            }
        }
    });
    $(document).on("paste",function(){ 
        //alert($("td.td-highlight").length);
        if ($("td.td-highlight").length == 1) {
            $("#a").val("");
            $("#a").focus();
            setTimeout(function(){
                //获取行列号       
                var rc = $("td.td-highlight").attr("name").split("-");
                var r = rc[0];
                var c = rc[1];
                //去掉最后一个换行符
                var value = $("#a").val();
                //alert(value.lastIndexOf("\n")+","+value.length);
                if (value.lastIndexOf("\n") == value.length-1) {
                    value = value.substr(0,value.length-1);
                }

                var rows = value.split("\n");
                var valid_data = {};
                for(var i = 0; i < rows.length; i++){
                    $("#row-"+r).removeClass("unique");//清除重复标志
                    rows[i] = rows[i].split("\t");
                    for (var j in rows[i]) {
                        var isvalue = 0;
                        rows[i][j] = blank_clear_and_return_value(rows[i][j]);
                        if (i > 0 && rows[i][j].length == 0) {
                            rows[i][j] = rows[i-1][j];
                        }
                        if (rows[i][j].length > 0) {
                            isvalue = 1;
                        }
                        $("[name='"+r+"-"+c+"']").html(rows[i][j]);
                        $("[name='"+r+"-"+c+"']").attr('isvalid',0);//验证前先把验证标识设为0
                        $("[name='"+r+"-"+c+"']").attr('value',rows[i][j]);//设置value值
                        $("[name='"+r+"-"+c+"']").attr('title',rows[i][j]);//设置title值，正常情况与value保持一致
                        if (rows[i][j].length > 0) {
                            var data_item = {};
                            data_item["col"] = $("[name='"+r+"-"+c+"']").attr('col');
                            data_item["value"] = $("[name='"+r+"-"+c+"']").attr('value');
                            valid_data[$("[name='"+r+"-"+c+"']").attr('id')] = data_item;
                        }
                        c++;
                        if (isvalue == 1) {
                            $("#row-"+r).attr("isvalue","1");
                        }
                    }
                    r++;
                    c = c - j - 1;
                }
                ajax_post("/console/model_valid",{"model":"wj","valid_data":valid_data},function(data){
                    if (data.suc == 1) {
                        //console.log(data.valid_data);
                        for (var item in data.valid_data) {

                        //console.log(data.valid_data[item]);
                            if(data.valid_data[item].valid === true){
                                //alert_flavr(item);
                                $("#"+item).attr("isvalid",1);
                                $("#"+item).attr("valid",$("#"+item).attr("value"));
                            } else {
                                $("#"+item).attr("title",data.valid_data[item].valid);
                            }
                        }
                        //检查重复
                        check_unique();
                    } else {
                        alert_flavr("验证过程异常");
                    }
                });
            },50);
        }
    });
    function blur_valid(input_id){
        clear_input_blank($("#"+input_id));
        var id = $("#"+input_id).parent('td').attr("id");
        $("#"+id).attr('value',$("#"+input_id).val());//设置value值
        $("#"+id).attr('title',$("#"+input_id).val());//设置title值，正常情况与value保持一致
        $("#"+id).html($("#"+input_id).val());

        $("#"+id).attr("isvalid",0);//验证前先把验证标识设为0
        var row = $("#"+id).attr("name").split("-")[0];
        check_row_available(row);//设置行有效性
        $("#row-"+row).removeClass("unique");//清除重复标志
        if ($("#"+id).attr("value").length > 0) {
            post_valid("#"+id);    
        }
        
    }
    function post_valid(selector){
        ajax_post("/console/model_valid",{"model":"wj","valid_col":$(selector).attr("col"),"valid_value":$(selector).attr("title")},function(data){
            if (data.suc == 1) {
                $(selector).attr("isvalid",1);
                $(selector).attr("valid",$(selector).attr("value"));
                //检查重复
                check_unique();
            } else {
                $(selector).attr("title",data.msg);
            }
        });
    }
    function check_row_available(i){
        var isvalue = 0;
        $("#row-"+i+" > td").each(function(){
            if ($(this).attr("value") != undefined && $(this).attr("value").length > 0) {
                isvalue = 1;
            }
        });
        $("#row-"+i).attr("isvalue",isvalue);
    }
    function check_unique(){
        //本表重复
        var unique = new Array;
        $("#input-data tr[isvalue=1]").each(function(){
            var index = $(this).attr("id").split("-")[1];
            var item = [$("#ild-"+index).attr("valid"),$("#sys-"+index).attr("valid"),$("#pipeline-"+index).attr("valid"),$("#vnum-"+index).attr("valid")];
            var exist = 0;
            for (var u in unique) {
                if (unique[u].toString() == item.toString()) {
                    exist = 1;
                }
            }
            if (exist == 1) {
                $("#row-"+index).addClass("unique");
            } else {
                unique.push(item);
                //ajax_post("/wj/unique_check",{"item":item},function(data){
                    //if (data.suc == 1) {
                        //$("#row-"+index).attr("unique",0);
                    //}
                //});
            }
        });
    }
    //数据提交
    function table_data_submit(){
        //验证是否有值
        if ($("#input-data tr[isvalue=1]").length == 0) {
            alert_flavr("没有任何数据");
        } else {
            //验证是否有空值
            if ($("#input-data tr[isvalue=1] td[not_null][valid='']").length > 0) {
                alert_flavr("数据未填满,浅黄色为必填");
            } else if ($("#input-data tr[isvalue=1] td[value!=''][isvalid=0]").length > 0) {
                alert_flavr("有不合法的数据，请修改");
            } else {
                var data = new Array;
                $("#input-data tr[isvalue=1]").each(function(){
                    var row = new Array;
                    $(this).find("td[value]").each(function(){
                       row[$(this).attr("col")] = $(this).attr("valid");
                    });
                    data.push($row);
                });
                ajax_post("/wj/table_input_submit",{"data":data},function(data){
                    if (data.suc == 1) {
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
                                    $('#current_iframe').attr('src','/console/status_avail_procedure?proc_id='+data.proc_id);
                                    return false;
                                }
                            },
                            close   : {
                                text    : '关闭'
                            }
                        },function(){
                            location.reload();
                        });
                    } else {
                        alert_flavr(data.msg);
                    }
                });
            }
        }
    }
</script>
@endpush