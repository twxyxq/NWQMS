@if($model->table_delete)

@define $input_class = 'form-control input-sm';

<div class="ajax_input form-group form-horizontal" model="{{ $model->get_table() }}" type="{{ $model->item->get_only() }}" @if(isset($collection->id)) for_id="{{$collection->id}}"" @endif nullable="except">

	<input type="hidden" name="_token" value="{{ csrf_token() }}">

	@foreach($model->item as $key => $item)
		@if ($key != "unique")
			@if ($item->only === false)
				@if ($item->input == "init")
					<label for="{{$key}}" class="col-sm-1 control-label" title="{{$item->name}}">{{$item->name}}</label>

					@if (is_array($item->restrict) && sizeof($item->restrict) > 0)
						<div class="col-sm-{{$item->size?$item->size:3}}">
							<select name="{{$key}}" class="{{$input_class}}" 
							@if($item->cal_trigger) 
								onchange="trigger_cal('{{$key}}')" 
							@elseif($item->cal_switch)
								onchange="cal_switch('{{array_to_string($item->cal_switch_item)}}','{{$key}}')"
							@endif
							>
								@foreach($item->restrict as $value)
									<option value="{{$value}}" @if(isset($collection->$key) && $collection->$key == $value) selected @endif>{{$value}}</option>
								@endforeach
							</select>
						</div>
					@else

						@define $attr = '';

						@define $blur_trigger = '';

						@if(isset($collection->$key)) 
							@define $attr .= ' value="'.$collection->$key.'" ' 
						@endif

						@if ($item->def=="null") 
							@define $attr .= ' nullable="1" ' 
						@endif

						@if (sizeof($item->bind) > 0)

							@if($item->cal_trigger)
								@define $attr .= ' change_fn="trigger_cal(\''.$key.'\')" '
							@endif

							@if ($item->multiple)
								<div class="col-sm-{{$item->size?$item->size:7}}">
									<input type="text" name="{{$key}}" class="{{$input_class}}" 
									@define echo $attr; 
									bind="{{$model->item->get_only()}}" multiples="{{$item->multiple}}">
								</div>
							@else
								<div class="col-sm-{{$item->size?$item->size:3}}">
									<input type="text" name="{{$key}}" class="{{$input_class}}" 
									@define echo $attr; 
									bind="{{$model->item->get_only()}}">
								</div>
							@endif
						@elseif ($item->type == "date")
							<div class="col-sm-{{$item->size?$item->size:3}}">
								<input type="text" class="form_date {{$input_class}}" id="{{$key}}" data-date-format="yyyy-mm-dd" name="{{$key}}" readonly="true" {{$attr}} />
							</div>
						@else
							
							@if(is_callable($item->restrict) || $item->cal_trigger)
								@define $blur_trigger = 1
							@endif

							@if(is_callable($item->restrict))
								@define $attr .= ' blur_valid="1" '
							@endif

							@if($item->cal_trigger)
								@define $attr .= ' blur_cal="1" '
							@endif


							@if($item->cal_result === true)
								@define $attr .= ' readonly="true" ';
							@elseif($item->cal_result === 1)
								@define $attr .= ' tips="<span style=\'position:absolute;top:0;right:5px;\'><label class=\'control-label\'><input type=\'checkbox\' id=\''.$key.'_cal\' checked>计算</label></span>" ';
							@elseif($item->cal_result)
								@define $attr .= ' is_cal="'.$item->cal_result.'" ';
								@define $input_class .= ' disabled';
							@endif


							<div class="col-sm-{{$item->size?$item->size:3}}">
								<input type="text" name="{{$key}}" class="{{$input_class}}" 

								@define echo $attr;

								@if(intval($blur_trigger) > 0)
									blurfn="{{$blur_trigger}}"
								@endif


								@if($item->history) 
									history="1" 
								@endif 

								@if(isset($collection->id) && in_array($key,$model->item->get_unique())) 
									readonly="true" 
								@endif

								@if($item->tip)
									tips="{{$item->tip}}"
								@endif
								
								>
							</div>
						@endif
					@endif
				@endif
			@else
				<input type="hidden" name="{{$key}}" value="{{$item->only}}">
			@endif
		@endif
	@endforeach
	<div class="col-sm-3" style="text-align: center">
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
