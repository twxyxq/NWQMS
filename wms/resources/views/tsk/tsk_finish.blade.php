@extends('layouts.panel_table')


@push('scripts')
<script type="text/javascript">
	$('#example').on( 'draw.dt', function () {
	    $("[for="+$("#example").attr("select_id")+"]").parent("td").parent("tr").find("td").addClass("row-select");
	});
	function add_finish_form(id){
		var postdata = {};
		postdata["id"] = id;
		postdata["_method"] = "PUT";
		postdata["_token"] = $("#_token").attr("value");
		ajax_post("/console/tsk_finish_form",postdata,function(rdata){
			if (Number(rdata.suc) == 1) {
				$(".panel-body").html(rdata.form);
				form_init();
				$("#example tr td").removeClass("row-select");
				$("#example").attr("select_id",id);
				$("[for="+id+"]").parent("td").parent("tr").find("td").addClass("row-select");
			} else {		
				alert_flavr(rdata.msg);
			}
		});
	}
</script>
@endpush