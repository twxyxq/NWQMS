@extends('layouts.panel_table')


@section('panel-body')
<div class="ajax_input form-group form-horizontal" model="setting" nullable="except">
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<input type="hidden" name="setting_type" value="<!--type-->">
	<label for="setting_name" class="col-sm-4 col-md-2 control-label"><!--type_name-->：</label>
	<div class="col-sm-8 col-md-2"><input type="text" name="setting_name" class="form-control"></div>
	<label for="setting_r0" class="col-sm-4 col-md-2 control-label"><!--r0-->：</label>
	<div class="col-sm-8 col-md-2"><input type="text" name="setting_r0" class="form-control" bind="<!--type-->"></div>
	<label for="setting_comment" class="col-sm-4 col-md-1 control-label">备注：</label>
	<div class="col-sm-6 col-md-2"><input type="text" name="setting_comment" class="form-control" nullable="0"></div>
	<div class="col-sm-2 col-md-1"><button class="btn btn-default ajax_submit">录入</button></div>
</div>
@endsection

