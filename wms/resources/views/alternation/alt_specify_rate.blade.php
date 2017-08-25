@extends('layouts.page')

@push('style')
	<style type="text/css">
		#task .form-control{
			width: 96%;
		}
		#task th,#task td{
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

	<div class="container">
		<table id="task" class="table table-striped table-hover">
			<thead>
				<th>操作</th>
				<th>焊口号</th>
				<th>规格材质</th>
				<th>变更情况</th>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</table>
	</div>
	<div class="ajax_input form-group form-horizontal" model="wj" type="" nullable="except">
		<div class="row">
			<div class="col-xs-10 col-xs-offset-1">
				指定的检验比例：
			</div>
			<div class="col-xs-10 col-xs-offset-1">
				<div class="col-sm-2"><label for="RT" class="col-xs-3">RT</label> <div class="col-xs-9"><input type="text" class="form-control input-sm" name="RT" size="3" blur_valid="1" blurfn="1" refer="1" history="1"></div></div>
				<div class="col-sm-2"><label for="UT" class="col-xs-3">UT</label> <div class="col-xs-9"><input type="text" class="form-control input-sm" name="UT" size="3" blur_valid="1" blurfn="1" history="1" refer="1"></div></div> 
				<div class="col-sm-2"><label for="PT" class="col-xs-3">PT</label> <div class="col-xs-9"><input type="text" class="form-control input-sm" name="PT" size="3" blur_valid="1" blurfn="1" history="1" refer="1"></div></div> 
				<div class="col-sm-2"><label for="MT" class="col-xs-3">MT</label> <div class="col-xs-9"><input type="text" class="form-control input-sm" name="MT" size="3" blur_valid="1" blurfn="1" history="1" refer="1"></div></div> 
				<div class="col-sm-2"><label for="SA" class="col-xs-3">SA</label> <div class="col-xs-9"><input type="text" class="form-control input-sm" name="SA" size="3" blur_valid="1" blurfn="1" history="1" refer="1"></div></div> 
				<div class="col-sm-2"><label for="HB" class="col-xs-3">HB</label> <div class="col-xs-9"><input type="text" class="form-control input-sm" name="HB" size="3" blur_valid="1" blurfn="1" history="1" refer="1"></div></div> 
			</div>
		</div>
		<div class="row">
			<textarea name="exam_specify_reason" class="col-xs-10 col-xs-offset-1 form-control" placeholder="指定检验比例原因"></textarea>
		</div>
		<div style="text-align: center;">
		    <button class="btn btn-success" onclick="submit_tsk()">确定</button>
		</div>
	</div>
</div>
@endsection

@push('scripts')
	<script type="text/javascript">

		function wj_choose(id){

			if ($("#wj_info_"+id).length == 0) {
				if ($("[name='ptest']").length == 0 || $("#ptest_"+id).html() == $("[name='ptest']").val()) {

					var method = new Array("RT","UT","PT","MT","SA","HB");

					var html = "<tr>";
					html += "<td><button id='wj_info_"+id+"' for='"+id+"'  class='wj_info btn btn-danger btn-small' onclick='remove_wj_info("+id+")'>删除</button></td>";
					html += "<td><strong>"+$("#vcode_"+id).html()+"</strong></ted>";
					html += "<td>"+$("#type_"+id).html()+"</td>";
					html += "<td>";
					for (var i = 0; i < method.length; i++) {
						html += method[i]+":"+$("#"+method[i]+"_"+id).html()+" ";
					}
					html += "</td>";
					html += "</tr>";

					$("#task > tbody").append(html);


					refresh_data();

				} else {
					alert_flavr("变更方向不一致");
				}
					
			}


		}

		function remove_wj_info(id){
			$("#wj_info_"+id).parent("td").parent("tr").remove();
			refresh_data();
		}

		function refresh_data(){
			var in_id = "";
			$(".wj_info").each(function(){
				in_id += ","+$(this).attr("for");
			});
			in_id = in_id.substr(1);
			$("#example").DataTable().settings()[0].ajax.data.indexNotIn = in_id;
			if ($(".wj_info").length == 0) {
				var ptest = "";
			} else {
				var ptest = $("[name='ptest']").val();
			}
			$("#example_wrapper .dataTables_scrollFoot .search_box").eq( 5 ).val( ptest );
			$("#example").DataTable().columns().eq( 0 ).each(function(colIdx){
				$("#example").DataTable().column( colIdx ).search( $(".dataTables_scrollFoot .search_box").eq( colIdx ).val() )
			});
			$("#example").DataTable().draw(false);
		}

		function submit_tsk(){
			if ($(".wj_info").length == 0) {
				alert_flavr("没有选择焊口");
			} else if(blank_clear_and_return_value($("[name='exam_specify_reason']").val()).length == 0){
				alert_flavr("请填写变更理由");
			} else {
				if (confirm("确定要变更？")) {
					var postdata = {};
					//postdata["data"] = new Array();
					postdata["exam_specify"] = 1;
					postdata["model"] = "wj";
					postdata["dirty"] = {
						"exam_specify" : 1,
						"exam_specify_reason" : $("[name='exam_specify_reason']").val(),
						"RT":$("[name='RT']").val(),
						"UT":$("[name='UT']").val(),
						"PT":$("[name='PT']").val(),
						"MT":$("[name='MT']").val(),
						"SA":$("[name='SA']").val(),
						"HB":$("[name='HB']").val()
					};
					postdata["original"] = {"exam_specify":0};
					postdata["id"] = new Array;
					$(".wj_info").each(function(){
						postdata["id"].push($(this).attr("for"));
					});
					postdata["_method"] = "PUT";
					postdata["_token"] = $("#_token").attr("value");
					//console.log(postdata);
					ajax_post("/console/alt_confirm", postdata, function(data){
						
						if (data.suc == 1) {
							$(".wj_info").parent("td").parent("tr").remove();
							$("[name='exam_specify_reason']").val("");
							$("[name='RT']").val("");
							$("[name='UT']").val("");
							$("[name='PT']").val("");
							$("[name='MT']").val("");
							$("[name='SA']").val("");
							$("[name='HB']").val("");
							refresh_data();
							dt_alt_exam_specify_proc(data.proc_id,"wj",postdata["id"]);		
						} else {		
							alert_flavr(data.msg);
						}
						
					});	
				}
					
			}
		}
	</script>
@endpush
