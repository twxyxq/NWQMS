@extends('layouts.page')

@section('content')
<div class="container">
	<div class="row">
	    <div class="col-md-10 col-md-offset-1">
	        <div class="panel panel-default">
	            <div class="panel-heading">
    				<a class="btn btn-success btn-small" href="/interior_management/overtime_personal">个人考勤</a> 
	            	<a class="btn btn-warning btn-small" href="/interior_management/overtime_examine_and_approve">考勤审批</a> 
	            	<a class="btn btn-info btn-small" href="/interior_management/overtime_statistic">考勤统计</a>
    			</div>
	            <div class="panel-body">
	            	<label for="overtime_date" class="col-xs-3 col-sm-1 control-label input-sm" title="日期">日期</label>
	            	<div class="col-xs-9 col-sm-3">
						<input type="text" name="overtime_date" class="form-control form_date input-sm" data-date-format="yyyy-mm-dd" readonly="true">
					</div>
	            	<div class="col-xs-12">
	            		<div class="col-xs-4 col-sm-2">
	            			<select class="form-control input-sm" placeholder="开始">
								@for($i = 0; $i < 24; $i++)
									<option value="{{$i}}">0{{$i}}:00</option>
									<option value="{{$i+0.5}}">00:30</option>
								@endfor
								<option value="24">24:00</option>
							</select>
	            		</div>
	            		<div class="col-xs-4 col-sm-2">
	            			<select class="form-control input-sm" placeholder="结束">
								@for($i = 0; $i < 24; $i++)
									<option value="{{$i}}">0{{$i}}:00</option>
									<option value="{{$i+0.5}}">00:30</option>
								@endfor
								<option value="24">24:00</option>
							</select>
	            		</div>
	            		<div class="col-xs-4 col-sm-2">
	            			<button class="btn btn-info btn-small">添加</button>
	            		</div>
					</div>

	            	<div class="col-xs-12">
	            		<button class="btn btn-success btn-small">确定</button>
	            	</div>
	            </div>
	        </div>
	    </div>
	</div>
</div>
<div>
	<!--datatables-->
    @include('conn/datatables')
</div>
@endsection
