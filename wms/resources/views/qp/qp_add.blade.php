@extends('layouts.panel_table')

@section('panel-body')
<div class="ajax_input form-group form-horizontal" model="qp" nullable="except">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	<label for="qp_project" class="col-sm-1 control-label">项目</label>
	<div class="col-sm-3">
		<select name="qp_project" class="form-control">
			<option value="常规岛安装">常规岛安装</option>
			<option value="BOP安装">BOP安装</option>
		</select>
	</div>
	<label for="qp_ild" class="col-sm-1 control-label">机组</label>
	<div class="col-sm-3"><input type="text" name="qp_ild" class="form-control" bind="[5,6,7,0]" multiple="1"></div>
	<label for="qp_sys" class="col-sm-1 control-label">系统</label>
	<div class="col-sm-3"><input type="text" name="qp_sys" class="form-control"></div>

	<label for="qp_code" class="col-sm-1 control-label">编码</label>
	<div class="col-sm-3"><input type="text" name="qp_code" class="form-control"></div>
	<label for="qp_name" class="col-sm-1 control-label">名称</label>
	<div class="col-sm-4"><input type="text" name="qp_name" class="form-control"></div>
	<label for="qp_version" class="col-sm-1 control-label">版本</label>
	<div class="col-sm-2">
		<select name="qp_version" class="form-control">
			<option value="A">A</option>
			<option value="B">B</option>
			<option value="C">C</option>
			<option value="D">D</option>
		</select>
	</div>

	<label for="qp_ild" class="col-sm-1 control-label">工序</label>
	<div class="col-sm-9"><input type="text" name="qp_proc_model" class="form-control" bind="[5,6,7,0]" multiple="1"></div>
	<div class="col-sm-2"><button class="btn btn-default ajax_submit">录入</button></div>
</div>
@endsection