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

			<label for="method" class="col-sm-1">焊接方法</label>
			<div class="col-sm-3">
				<select name="method" class="like form-control input-sm" like="qf_info">
					<option value="">无限制</option>
					<option value="%HD%">HD</option>
					<option value="%HWS%">HWS</option>
					<option value="%HS%">HS</option>
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
					<option value="% GW %,% FW %">GW</option>
					<option value="% FW %">FW</option>
					<option value="% D %">D</option>
				</select>
			</div>


			<label for="diameter" class="col-sm-1">管径大小</label>
			<div class="col-sm-3">
				<input name="diameter" class="form-control input-sm">
			</div>

	</div>
@endsection

@push('scripts')
<script type="text/javascript">

	function restrict(){
		var like = "";
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

	}



	$(".like").on("change",function(){
		
		if (like.length > 0) {
			$("#example").DataTable().settings()[0].ajax.data.like = like.substr(1);
		} else {
			delete $("#example").DataTable().settings()[0].ajax.data.like;
		}
		$("#example").DataTable().draw(false);
	})
</script>
@endpush