@extends('layouts.page')

@push('style')
	<style type="text/css">
		#group .form-control{
			width: 96%;
		}
		#group th,#group td{
			text-align: center;
		}
		#exam_group {
			background-color: #FFFEEF;
			border: 1px solid #DAD9D9;
		}
		#group_title {
			text-align: center;
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
	            	@include('conn/datatables')
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div>

<div id="exam_group" class="container"">
	<div class="row">
		<div id="group_title">
			<strong><h4>《<span id="g_title"></span>》</h4></strong>
			<input type="hidden" id="g_pp">
		</div>
		<div class="col-sm-4">
			<strong>检验方法：</strong><span id="g_emethod">{{$_GET["emethod"]}}</span>
		</div>
		<div class="col-sm-4">
			<strong>类型：</strong><span id="g_type"></span>
		</div>
		<div class="col-sm-4">
			<strong>比例：</strong><span id="g_rate"></span>%
		</div>
		<div class="col-sm-4">
			<strong>系统：</strong><span id="g_ild_sys"></span>
		</div>
		<div class="col-sm-4">
			<strong>焊工：</strong><span id="g_pp_show"></span>
		</div>
		<div class="col-sm-4">
			<strong>工艺：</strong><span id="g_wps"></span>
		</div>
	</div>
	<div class="well">
		<table id="group" class="table table-striped table-hover">
			<thead>
				<th>操作</th>
				<th>焊口号</th>
				<th>规格</th>
			</thead>
			<tbody>
				
			</tbody>
			<tfoot>
				<th colspan="5">
					<button class="btn btn-success" onclick="submit_tsk()">确定</button>
				</th>
			</tfoot>
		</table>
	</div>
</table>
</div>
    
</div>
@endsection

@push('scripts')
	<script type="text/javascript">

		function wj_choose(id){
			var permit = 0
			if ($("#g_title").html().length == 0 && $("#g_type").html().length == 0 && $("#g_ild_sys").html().length == 0  && $("#g_rate").html().length == 0 && $("#g_pp").val().length == 0 && $("#g_wps").html().length == 0) {
				$("#g_title").html($("#identity_"+id).val());
				$("#g_ild_sys").html($("#ild_sys_"+id).val());
				$("#g_rate").html($("#rate_"+id).val());
				$("#g_pp_show").html($("#tsk_pp_show_"+id).html());
				$("#g_wps").html($("#wps_"+id).html());
				$("#g_type").html($("#wj_type_"+id).html());
				$("#g_pp").val($("#tsk_pp_"+id).val());
				permit = 1;
			} else if($("#g_ild_sys").html() == $("#ild_sys_"+id).val() && $("#g_type").html() == $("#wj_type_"+id).html() && $("#g_rate").html() == $("#rate_"+id).val() && $("#g_pp").val() == $("#tsk_pp_"+id).val() && $("#g_wps").html() == $("#wps_"+id).html()) {
				permit = 1;
			}
			if (permit ==1) {
				if ($("#wj_info_"+id).length == 0) {
					var html = "<tr>";
					html += "<td><button id='wj_info_"+id+"' for='"+id+"'  class='wj_info btn btn-danger btn-small' onclick='remove_wj_info("+id+")'>删除</button></td>";
					html += "<td>"+$("#vcode_"+id).html()+"</td>";
					html += "<td>"+$("#type_"+id).html()+"</td>";
					html += "</tr>";

					$("#group > tbody").append(html);
				}

				refresh_data();
			} else {
				alert_flavr("该焊口不能作为同一组");
			}
			

		}

		function remove_wj_info(id){
			id = typeof(id)=="undefined"?0:id;
			if (id != 0) {
				$("#wj_info_"+id).parent("td").parent("tr").remove();
			}
			if ($(".wj_info").length == 0) {
				$("#g_title").html("");
				$("#g_type").html("");
				$("#g_ild_sys").html("");
				$("#g_rate").html("");
				$("#g_pp_show").html("");
				$("#g_wps").html("");
				$("#g_pp").val("");
			}
			refresh_data();
		}

		function refresh_data(){
			var in_id = "";
			$(".wj_info").each(function(){
				in_id += ","+$(this).attr("for");
			});
			in_id = in_id.substr(1);
			$("#example").DataTable().settings()[0].ajax.data.indexNotIn = in_id;
			$("#example_wrapper .dataTables_scrollFoot .search_box").eq( 1 ).val( $("#g_type").html() );
			$("#example_wrapper .dataTables_scrollFoot .search_box").eq( 2 ).val( $("#g_ild_sys").html() );
			$("#example_wrapper .dataTables_scrollFoot .search_box").eq( 4 ).val( $("#g_rate").html() );
			$("#example_wrapper .dataTables_scrollFoot .search_box").eq( 5 ).val( $("#g_pp_show").html() );
			$("#example_wrapper .dataTables_scrollFoot .search_box").eq( 6 ).val( $("#g_wps").html() );
			$("#example").DataTable().columns().eq( 0 ).each(function(colIdx){
				$("#example").DataTable().column( colIdx ).search( $(".dataTables_scrollFoot .search_box").eq( colIdx ).val() )
			});
			$("#example").DataTable().draw(false);
		}

		function submit_tsk(){
			if ($(".wj_info").length == 0) {
				alert_flavr("没有选择焊口");
			} else {
				if (confirm("确认该分组信息？分组成功后不可撤销")) {
					var postdata = {};
					postdata["wj_ids"] = new Array();
					$(".wj_info").each(function(){;
						postdata["wj_ids"].push($(this).attr("for"));
					});
					postdata["code"] = $("#g_title").html();
					postdata["emethod"] = $("#g_emethod").html();
					postdata["wj_type"] = $("#g_type").html();
					postdata["ild_sys"] = $("#g_ild_sys").html();
					postdata["rate"] = $("#g_rate").html();
					postdata["pp_show"] = $("#g_pp_show").html();
					postdata["wps"] = $("#g_wps").html();
					postdata["_method"] = "PUT";
					postdata["_token"] = $("#_token").attr("value");
					//console.log(postdata);
					$.post("/consignation/consignation_add", postdata, function(data){
						if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
							alert_flavr("操作失败！错误信息："+data);
						} else {
							eval('var rdata = '+data);
							//alert(rdata.suc); 
							if (Number(rdata.suc) == 1) {
								if (alert_flavr(rdata.msg));
								$(".wj_info").parent("td").parent("tr").remove();
								remove_wj_info();						
							} else {		
								alert_flavr(rdata.msg);
							}
						}
						
					});
				}
			}
		}
	</script>
@endpush
