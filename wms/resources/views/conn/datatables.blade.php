<style type="text/css">
	.datatable_container{
		font-size: 12px;
	}
	.datatable_container table{
		font-size: 12px;
	}
	#example{
		margin:0;
		border: 1px solid;
	}
	.datatable_container td,.datatable_container th{
		max-width: 200px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		text-align: center;
	}
	.datatable_container td > span{
		width: 100%;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		text-align: center;
		display: block;
	}
	.datatable_container{
		position: relative;
		padding: 0;
		margin: 0 auto;
		max-width: 97vw;
		overflow-x: auto;
	}
	.search_box{
		min-width: 1px;
		width: 100%;
		text-align: center;
		font-size: 11px;
		font-weight: normal;
		border: 1px solid lightgray;
	}
	#datatable_output{
		position: absolute;
		top: 2px;
		left: 150px;
		z-index: 1;
	}
	#datatable_output div{
		display: inline-block;
		margin: 0 3px;
	}
	#example tfoot th:first-child:hover~th > input{
		border-color: pink;
	}
	#example tbody td{
		padding: 2px;
	}

	@media (max-width:780px){
		#datatable_output{
			display: none;
		}
	}
</style>
<div class="datatable_container">
@if(strpos($_SERVER["HTTP_USER_AGENT"], "MicroMessenger") === false)
	@if(!isset($no_output))
	<div id="datatable_output">
		<div id="output_all">
			<a href="###" target="blank"><span class="glyphicon glyphicon-download-alt"></span>全部</a>
		</div>
		<div id="output_filter">
			<a href="###" target="blank"><span class="glyphicon glyphicon-download-alt"></span>筛选</a>
		</div>
		<div id="output_view">
			<a href="###" target="blank"><span class="glyphicon glyphicon-download-alt"></span>页面</a>
		</div>
	</div>
	@endif
@endif
<table id="example" class="display compact" cellspacing="0" width="{{isset($width)?$width:"100%"}}">
	<thead>
		<tr>
			<!--datatables.th-->
			@foreach($datatables_th as $th)
				<th>{{$th}}</th>
			@endforeach
		</tr>
	</thead>
	@if(isset($dataset))
	<tbody>
		@for($i = 0; $i < sizeof($dataset); $i++)
		<tr>
			@foreach($dataset[$i] as $dataset_value)
			<td>{!!$dataset_value!!}</td>	
			@endforeach
		</tr>
		@endfor
	</tbody>
	@endif
	<tfoot>
		<tr>
			<!--datatables.th-->
			@foreach($datatables_th as $th)
				<th>{{$th}}</th>
			@endforeach
		</tr>
	</tfoot>
</table>
</div>
@push('scripts')
<script type="text/javascript">
	function reset_search(){
		$('#example tfoot th input').each( function () {
			$("#example_wrapper .search_box").val("");
		});
		$("#example").DataTable().columns().search("").draw();
	}
	$(document).ready(function(){
		// Setup - add a text input to each footer cell

		var z = 0;
		$('#example tfoot th').each( function () {
			if (z == 0) {
				$(this).html( '<input class="btn btn-default btn-small search_box" type="text" placeholder="重置" readonly="true" onclick="reset_search();" />' );
			} else {
				var title = $('#example thead th').eq( $(this).index() ).text();
				$(this).html( '<input class="search_box" type="text" placeholder="筛选:'+title+'" />' );
			}
			z++;
		});

		// DataTable
		var table = $('#example').DataTable({
			@if(!isset($dataset))
			processing: true,
			serverSide: true,
			scrollX: true,
			//stateSave: true,
			//autoWidth: false,//
			ajax: {
				url: "{!!$datatables_url!!}",
				data: {
					{!!$datatables_data!!}
				}
			},
			@endif
			language: {
				"sProcessing": "数据处理中...",
				"sLengthMenu": "显示 _MENU_ ",
				"sZeroRecords": "没有匹配结果",
				"sInfo": "第 _START_ 至 _END_ 项，共 _TOTAL_ 项",
				"sInfoEmpty": "第 0 至 0 项，共 0 项",
				"sInfoFiltered": "(由 _MAX_ 项结果过滤)",
				"sInfoPostFix": "",
				"sSearch": "搜索",
				"sUrl": "",
				"sEmptyTable": "表中数据为空",
				"sLoadingRecords": "载入中...",
				"sInfoThousands": ",",
				"oPaginate": {
				    "sFirst": "首页",
				    "sPrevious": "上页",
				    "sNext": "下页",
				    "sLast": "末页"
				},
				"oAria": {
				    "sSortAscending": ": 以升序排列此列",
				    "sSortDescending": ": 以降序排列此列"
				}
			}
			{!!$datatables_setting!!}
		});
	 
		// Apply the search
		table.columns().eq( 0 ).each( function ( colIdx ) {
			$( 'input', table.column( colIdx ).footer() ).on( 'keyup change', function () {
				table
					.column( colIdx )
					.search( this.value )
					.draw();
			} );

		});
		var title = "";
		$("#example thead tr th").each(function(){
			title += ","+$(this).html();
		});
		title = title.substr(1);
		var auto_index = 0;
		//auto_index_set//
		@if(!isset($dataset))
			$("#output_all a").click(function(){
				$("#output_all a").attr("href","{!!$datatables_url!!}&title={{urlencode(array_to_multiple($datatables_th))}}&output=1&all=1&"+$.param($('#example').DataTable().ajax.params()));
			});
			$("#output_filter a").click(function(){
				$("#output_filter a").attr("href","{!!$datatables_url!!}&title={{urlencode(array_to_multiple($datatables_th))}}&output=1&filter=1"+$.param($('#example').DataTable().ajax.params()));
			});
			$("#output_view a").click(function(){
				$("#output_view a").attr("href","{!!$datatables_url!!}&title={{urlencode(array_to_multiple($datatables_th))}}&output=1&view=1"+$.param($('#example').DataTable().ajax.params()));
			});
		@else
			$("#output_all a").click(function(){
				$("#output_all a").attr("href","{!!url()->current().'?'.(strlen($_SERVER['QUERY_STRING'])>0?'&':'').'output=1'!!}");
			});
			$("#output_filter").remove();
			$("#output_view").remove();
		@endif

		
	});
</script>
@endpush