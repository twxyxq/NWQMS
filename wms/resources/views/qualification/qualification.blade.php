@extends('layouts.panel_table')

@push('style')
<style type="text/css">
	#ccc > label{
		white-space: nowrap;
		margin-bottom: 0;
		line-height: 32px;
	}
</style>
@endpush

@section('panel-body')
	<div id="ccc" class="form-group form-horizontal">

			<div class="col-sm-12"><strong>请输入待焊部件信息（下方列表显示的为匹配的资质）：</strong></div>

			<label for="wmethod" class="col-sm-1">焊接方法</label>
			<div class="col-sm-3">
				<select name="wmethod" class="like form-control input-sm" like="qf_info">
					<option value="">无限制</option>
					<option value="HD">HD</option>
					<option value="HWS">HWS</option>
					<option value="HS">HS</option>
				</select>
			</div>
			

			<label for="jtype" class="col-sm-1">试件型式</label>
			<div class="col-sm-3">
				<select name="jtype" class="form-control input-sm"">
					<option value="">无限制</option>
					<option value="P">P</option>
					<option value="T">T</option>
					<option value="P-T">P-T</option>
					<option value="T-T">T-T</option>
				</select>
			</div>
			

			<label for="gtype" class="col-sm-1">焊缝型式</label>
			<div class="col-sm-3">
				<select name="gtype" class="like form-control input-sm" like="qf_info">
					<option value="">无限制</option>
					<option value="GW">GW</option>
					<option value="FW">FW</option>
					<option value="D">D</option>
				</select>
			</div>


			<label for="diameter" class="col-sm-1">管径大小</label>
			<div class="col-sm-3">
				<input name="diameter" class="form-control input-sm">
			</div>

			<label for="thickness" class="col-sm-1">厚度大小</label>
			<div class="col-sm-3">
				<input name="thickness" class="form-control input-sm">
			</div>


			<label for="position" class="col-sm-1">焊接位置</label>
			<div class="col-sm-3">
				<select name="position" class="like form-control input-sm">
					<option value="">无限制</option>
					<option value="PA">PA</option>
					<option value="PB">PB</option>
					<option value="PC">PC</option>
					<option value="PD">PD</option>
					<option value="PE">PE</option>
					<option value="PF">PF</option>
					<option value="PG">PG</option>
					<option value="H-L045">H-L045</option>
					<option value="J-L045">J-L045</option>
				</select>
			</div>


			<label for="baseA" class="col-sm-1">材质A</label>
			<div class="col-sm-3">
				<select name="baseA" class="like form-control input-sm">
					<option value="0">无限制</option>
					<option value="1">Ⅰ</option>
					<option value="2">Ⅱ</option>
					<option value="3">Ⅲ</option>
					<option value="4">Ⅳ</option>
					<option value="5">Ⅴ</option>
					<option value="6">Ⅵ</option>
					<option value="7">Ⅶ</option>
					<option value="8">Ⅷ</option>
					<option value="9">Ⅸ</option>
				</select>
			</div>

			<label for="baseB" class="col-sm-1">材质B</label>
			<div class="col-sm-3">
				<select name="baseB" class="like form-control input-sm">
					<option value="0">无限制</option>
					<option value="1">Ⅰ</option>
					<option value="2">Ⅱ</option>
					<option value="3">Ⅲ</option>
					<option value="4">Ⅳ</option>
					<option value="5">Ⅴ</option>
					<option value="6">Ⅵ</option>
					<option value="7">Ⅶ</option>
					<option value="8">Ⅷ</option>
					<option value="9">Ⅸ</option>
				</select>
			</div>


			<label for="parameter" class="col-sm-1">焊接要素</label>
			<div class="col-sm-3">
				<select name="parameter" class="like form-control input-sm">
					<option value="">无限制</option>
					<option class="GW" value="ss nb">单面焊/不带垫板</option>
					<option class="GW" value="ss mb">单面焊/带垫板</option>
					<option class="GW" value="bs">双面焊</option>
					<option class="FW" value="sl">单层</option>
					<option class="FW" value="ml">多层</option>
				</select>
			</div>



			<label for="xyz" class="col-sm-1">专项代号</label>
			<div class="col-sm-3">
				<select name="xyz" class="like form-control input-sm">
					<option value="N">非特殊</option>
					<option value="Z">插套</option>
				</select>
			</div>

			<div class="row col-sm-12" style="background-color:#FBE8FD">
				<label for="name" class="col-sm-3 col-sm-offset-1 control-label">※ 信息保存：</label>
				<div class="col-sm-6">
					<input type="text" name="name" class="form-control input-sm">
				</div>
				<div class="col-sm-1" style="line-height: 30px">
					<button class="btn btn-success btn-small" onclick="range_save()">保存</button>
				</div>
			</div>


	</div>
@endsection

@push('scripts')
<script type="text/javascript">

	function get_val(d){
		ajax_post("/pp/get_val",{"val":d},function(data){

		});
	}

	function range_save(){
		if ($("[name='name']").val().length > 0) {
			var postdata = {};
			//postdata["qf_range_name"] = $("[name='qf_range_name']").val();
			$("#ccc select,#ccc input").each(function(){
				postdata[$(this).attr("name")] = $(this).val();
			});
			ajax_post("/pp/range_save",postdata,function(data){
				if (data.suc == 1) {
					alert_flavr("保存成功",function(){
						location.reload();
					});
				} else {
					alert_flavr(data.msg);
				}
			});
		} else {
			alert_flavr("保存名称不能为空");
		}
	}
	/*
	function restrict(){
		var like = "";
		var where = "";
		$(".like").each(function(){
			if ($(this).val().length > 0) {
				like += "#"+$(this).attr("like")+","+$(this).val();
			}
		});
		if ($("[name='jtype']").val() == "P-T") {
			like += "#qf_info,% P-T %";
		} else if ($("[name='jtype']").val() == "T-T") {
			like += "#qf_info,% T-T %,% P-T %";
		} else if ($("[name='jtype']").val() == "P") {
			like += "#qf_info,% P %";
		} else if ($("[name='jtype']").val() == "T") {
			if ($("[name='diameter']").val().length == 0 || $("[name='diameter']").val() < 25) {
				like += "#qf_info,% T %";
			} else {
				like += "#qf_info,% T %,% P %";
			}
		}
		if (like.length > 0) {
			$("#example").DataTable().settings()[0].ajax.data.like = like.substr(1);
		} else {
			delete $("#example").DataTable().settings()[0].ajax.data.like;
		}
		$("#example").DataTable().draw(false);
	}
	*/
	$(document).ready(function(){
		$("[name='diameter']").attr("readonly",true);
		$("[name='thickness']").attr("readonly",true);
	});

	function restrict(){
		//数值有效性
		if ($("[name='jtype']").val() == "P") {
			$("[name='diameter']").attr("readonly",true);
			$("[name='diameter']").val("");
		} else {
			$("[name='diameter']").attr("readonly",false);
		}

		if (isNaN($("[name='diameter']").val())) {
			$("[name='diameter']").val("");
		}

		if (isNaN($("[name='thickness']").val())) {
			$("[name='thickness']").val("");
		}

		if ($("[name='gtype']").val() == "GW") {
			$(".FW").css("display","none");
			$(".GW").css("display","");
			$("[name='diameter']").attr("readonly",false);
			$("[name='thickness']").attr("readonly",false);
		} else if ($("[name='gtype']").val() == "FW") {
			$(".GW").css("display","none");
			$(".FW").css("display","");
			$("[name='thickness']").attr("readonly",false);
			$("[name='diameter']").attr("readonly",true);
			$("[name='diameter']").val("");
		} else if ($("[name='gtype']").val() == "D") {
			$(".GW").css("display","none");
			$(".FW").css("display","none");
			$("[name='thickness']").attr("readonly",false);
			$("[name='diameter']").attr("readonly",true);
			$("[name='diameter']").val("");
		} else {
			$(".GW").css("display","");
			$(".FW").css("display","");
			$("[name='diameter']").attr("readonly",true);
			$("[name='diameter']").val("");
			$("[name='thickness']").attr("readonly",true);
			$("[name='thickness']").val("");
		}



		var para = "";
		$("#ccc select,#ccc input").each(function(){
			$("#example").DataTable().settings()[0].ajax.data[$(this).attr("name")] = $(this).val();
			//para += "&"+$(this).attr("name")+"="+$(this).val();
		});
		//location.href = "?"+para.substr(1);
		$("#example").DataTable().draw(false);
	}



	$("#ccc select").on("change",function(){
		restrict();
	});
	$("#ccc input").on("blur",function(){
		restrict();
	});
</script>
@endpush