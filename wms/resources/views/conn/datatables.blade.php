<style type="text/css">
	#example{
		margin:0;
		border: 1px solid;
		font-size: 13px;
	}
	#example td,#example th{
		white-space: nowrap;
		max-width: 18vw;
		overflow: hidden;
		text-overflow: ellipsis;
		text-align: center;
	}
	.datatable_container{
		position: relative;
		width: 100%;
		padding: 0;
		margin: 0;
		max-width: 100vw;
		overflow-x: auto;
	}
	.search_box{
		min-width: 1px;
		width: 100%;
		text-align: center;
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
	@media (max-width:780px){
		#datatable_output{
			display: none;
		}
	}
</style>
<div class="datatable_container">
<div id="datatable_output">
	<div id="output_all">
		<a href="###"><span class="glyphicon glyphicon-download-alt"></span>全部</a>
	</div>
	<div id="output_filter">
		<a href="###"><span class="glyphicon glyphicon-download-alt"></span>筛选</a>
	</div>
	<div id="output_view">
		<a href="###"><span class="glyphicon glyphicon-download-alt"></span>页面</a>
	</div>
	<div id="output_office">
		<select id="office_select">
			<option value="wps">wps</option>
			<option value="msoffice">MS</option>
		</select>
	</div>
</div>
<table id="example" class="display compact" cellspacing="0" width="100%">
	<thead>
		<tr>
			<!--datatables.th-->
		</tr>
	</thead>
	<tfoot>
		<tr>
			<!--datatables.th-->
		</tr>
	</tfoot>
</table>
</div>
@push('scripts')
<script type="text/javascript">
	function reset_search(){
		$('#example tfoot th input').each( function () {
			$(this).val("");
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
				$(this).html( '<input class="search_box" type="text" placeholder="筛选 '+title+'" />' );
			}
			z++;
		});

		// DataTable
		var table = $('#example').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "//datatables.url//",
				data: {
					//datatables.data//
				}
			},
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
			//datatables.setting//
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
		$("#output_all a").click(function(){
			$(this).attr("href","/console/output.console.php?title="+title+"&query_word="+$("#all_query").attr("value")+"&office_type="+$("office_select").attr("value")+"&auto_index=1");
		});
		$("#output_filter a").click(function(){
			$(this).attr("href","/console/output.console.php?title="+title+"&query_word="+$("#filter_query").attr("value")+"&office_type="+$("office_select").attr("value")+"&auto_index=1");
		});
		$("#output_view a").click(function(){
			$(this).attr("href","/console/output.console.php?title="+title+"&query_word="+$("#view_query").attr("value")+"&office_type="+$("office_select").attr("value")+"&auto_index=1");
		});

		
	});
</script>
@endpush