@if($model->table_delete)
<div class="ajax_input form-group form-horizontal" model="{{ $model->get_table() }}" type="{{ $model->item->get_only() }}" @if(isset($collection->id)) for_id="{{$collection->id}}"" @endif nullable="except">

	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	@foreach($model->item as $key => $item)
		@if ($key != "unique")
			@if ($item->only === false)
				@if ($item->input == "init")
					<label for="{{$key}}" class="col-sm-1 control-label" title="{{$item->name}}">{{$item->name}}</label>
					@if (sizeof($item->restrict) > 0)
						<div class="col-sm-3">
							<select name="{{$key}}" class="form-control">
								@foreach($item->restrict as $value)
									<option value="{{$value}}" @if(isset($collection->$key) && $collection->$key == $value) selected @endif>{{$value}}</option>
								@endforeach
							</select>
						</div>
					@elseif (sizeof($item->bind) > 0)
						@if ($item->multiple)
							<div class="col-sm-7">
								<input type="text" name="{{$key}}" class="form-control" @if(isset($collection->$key)) value="{{$collection->$key}}"" @endif @if ($item->def=="null") nullable="1" @endif bind="{{$model->item->get_only()}}" multiples="{{$item->multiple}}">
							</div>
						@else
							<div class="col-sm-3">
								<input type="text" name="{{$key}}" class="form-control" @if(isset($collection->$key)) value="{{$collection->$key}}"" @endif @if ($item->def=="null") nullable="1" @endif  bind="{{$model->item->get_only()}}">
							</div>
						@endif
					@elseif ($item->type == "date")
						<div class="col-sm-3">
							<input type="text" class="form_date form-control" id="{{$key}}" data-date-format="yyyy-mm-dd" name="{{$key}}" readonly="true" @if(isset($collection->$key)) value="{{$collection->$key}}"" @endif @if ($item->def=="null") nullable="1" @endif />
						</div>
					@elseif ($item->history)
						<div class="col-sm-3">
							<input type="text" name="{{$key}}" class="form-control" history="1" @if(isset($collection->$key)) value="{{$collection->$key}}" @endif @if ($item->def=="null") nullable="1" @endif @if(isset($collection->id) && in_array($key,$model->item->get_unique())) readonly="true" @endif>
						</div>
					@else
						<div class="col-sm-3">
							<input type="text" name="{{$key}}" class="form-control" @if(isset($collection->$key)) value="{{$collection->$key}}" @endif @if ($item->def=="null") nullable="1" @endif @if(isset($collection->id) && in_array($key,$model->item->get_unique())) readonly="true" @endif>
						</div>
					@endif
				@endif
			@else
				<input type="hidden" name="{{$key}}" value="{{$item->only}}">
			@endif
		@endif
	@endforeach
	<div class="col-sm-2">
		<button class="btn btn-default ajax_submit">
			@if(isset($collection))
				修改
			@else
				录入
			@endif
		</button>
	</div>
</div>
@else
该项目已经被锁定
@endif