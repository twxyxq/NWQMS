@if($model->table_delete)

	@if(!isset($collection) || (!isset($alt) && isset($collection) && $collection->valid_updating()) || (isset($alt) && !isset($_GET["view"]) && isset($collection) && !$model->valid_version_and_status($collection->current_version,$collection->status,$collection->procedure) && strlen($collection->procedure == 0)))

	<div class="ajax_input form-group form-horizontal" model="{{ $model->get_table() }}" type="{{ $model->item->get_only() }}" @if(isset($collection->id)) for_id="{{$collection->id}}"" @endif nullable="except">

		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		@if(isset($alt))
			<input type="hidden" name="_alt" value="{{$alt}}">
		@endif

		@if(isset($_GET["auth"]))
			<input type="hidden" name="_auth" value="{{$_GET['auth']}}">
		@endif

		@foreach($model->item as $key => $item)
			@if ($key != "unique")
				@if ($item->only === false)
					@if ($item->input == "init")

						@if(isset($hidden) && in_array($key,$hidden))

							<input type="hidden" name="{{$key}}" value="{{isset($lock[$key])?$lock[$key]:''}}">

						@else

							<label for="{{$key}}" class="col-sm-1 control-label" title="{{$item->name}}">{{$item->name}}</label>

							@define $input_class = 'form-control input-sm';

							@if(!isset($lock[$key]))


								@if(isset($collection->id) && $model->item->get_unique() !== false && in_array($key,$model->item->get_unique())) 
									
									<div class="col-sm-{{$item->size?$item->size:3}}">
										<input type="text" name="{{$key}}" class="{{$input_class}} disabled" readonly="true" value="{{$collection->$key}}">
									</div>

								@else

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
											@define $attr .= ' value='.(strlen($collection->$key)==0?'""':$collection->$key).' ' 
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
											@elseif($cal_result = $item->cal_result)
												@define $attr .= ' is_cal="'.$cal_result.'" ';
												@if((!isset($collection->$cal_result) || $collection->$cal_result == 0) && (!isset($lock[$cal_result]) || $lock[$cal_result] == 0))
													@define $input_class .= ' disabled';
												@endif
											@endif


											<div class="col-sm-{{$item->size?$item->size:3}}">
												<input type="text" name="{{$key}}" class="{{$input_class}}" 

												@define echo $attr;

												@if(intval($blur_trigger) > 0)
													blurfn="{{$blur_trigger}}"
												@endif

												@if($item->placeholder)
													placeholder="{{$item->placeholder}}"
												@endif

												@if($item->history) 
													history="1" 
												@endif 

												@if(isset($collection->id) && $model->item->get_unique() !== false && in_array($key,$model->item->get_unique())) 
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
								<div class="col-sm-{{$item->size?$item->size:3}}">
									<input type="text" name="{{$key}}" class="{{$input_class}}" readonly="true" value="{{$lock[$key]}}">
								</div>
							@endif
						@endif
					@endif
				@else
					<input type="hidden" name="{{$key}}" only="1" value="{{$item->only}}">
				@endif
			@endif
		@endforeach
		<div class="col-sm-3" style="text-align: center">
			@if(isset($alt))
				@define $b_text = "变更"
			@elseif(isset($collection))
				@define $b_text = "修改"
			@else
				@define $b_text = "录入"
			@endif
			<button class="btn btn-default ajax_submit" title="{{$b_text}}">
				{{$b_text}}
			</button>
		</div>
	</div>

	@else

		<div class="col-sm-12" style="padding: 10px">
            <span class="glyphicon glyphicon-info-sign"></span> 基础信息
        </div>
        @foreach($model->item as $key => $item)
            @if($item->input == "init")
                <div class="col-sm-4" style="padding: 0 3px">
                    <span class="form-control transparent-input restrict-show">
                    	◇{{$item->name}}：
                    	@if(isset($proc_info) && $proc_info != null && isset($proc_info->dirty->$key))
                    		<del style="color:red">{{$collection->$key}}</del> {{$proc_info->dirty->$key}}
                    	@else
                    		{{$collection->$key}}
                    	@endif
                    </span>
                </div>
            @endif
        @endforeach
        <div class="col-sm-12" style="padding: 10px">
        	※所有者：{{$collection->owner>0?\App\user::find($collection->owner)->name:"无"}} &nbsp; 
            ※创建人：{{$collection->created_by>0?\App\user::find($collection->created_by)->name:"无"}}  &nbsp; ※创建时间：{{$collection->created_at}}  &nbsp; 
            ※流程：{{$collection->procedure}}
        </div>
	
	@endif

@else
该项目已经被锁定
@endif
