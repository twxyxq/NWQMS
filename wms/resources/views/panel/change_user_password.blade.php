@extends('layouts.page_detail')

@push('style')
	<style type="text/css">
		#change_password > div {
			margin-bottom: 5px;
		}
	</style>
@endpush

@section('panel-body')
    <div id="change_password" class="row">
		<div class="col-sm-12">
			<label class="col-sm-5 control-label">原密码</label>
			<div class="col-sm-7">
				<input type="password" name="old" class="form-control">
			</div>
		</div>
		<div class="col-sm-12">
			<label class="col-sm-5 control-label">新密码</label>
			<div class="col-sm-7">
				<input type="password" name="new" class="form-control">
			</div>
		</div>
		<div class="col-sm-12">
			<label class="col-sm-5 control-label">确认密码</label>
			<div class="col-sm-7">
				<input type="password" name="confirm" class="form-control">
			</div>
		</div>
		<div class="col-sm-12" style="text-align: center;">
			<button class="btn btn-success" onclick="change_password()">确认</button>
		</div>
	</div>
@endsection

@push('scripts')
	<script type="text/javascript">
		function change_password(){
			var postdata = {};
			var pass = 1;
			$("#change_password [type='password']").each(function(){
				if ($(this).val().length == 0) {
					pass = 0
				}
				postdata[$(this).attr("name")] = $(this).val();
			});
			if (pass == 1) {
				ajax_post("/panel/change_password_exec",postdata,function(data){
					if (data.suc == 1) {
						alert_flavr(data.msg,function(){
							location.reload();
						});
					} else {
						alert_flavr(data.msg);
					}
				});
			} else {
				alert_flavr("输入不能为空");
			}
		}
	</script>
@endpush

