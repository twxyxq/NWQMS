//$(".ajax_input").ready(function(){
	//if ($(this).attr("for_id") != undefined) {

	//}
//});
function valid_null(form_obj){
	var find_text = "input[type=text]:not([nullable]):not([sp]),div[type=divtext]:not([nullable])";
	var null_num = 0;
	form_obj.find(find_text).each(function(){
		var isnull = 0;
		if ($(this).children("input[type=hidden]").length > 0) {
			if ($(this).children("input[type=hidden]").attr("value").length == 0) {
				isnull = 1;
			}
		} else if ($(this).val().length == 0 && $(this).find("input[type=checkbox]").length == 0) {
			isnull = 1;
		}
		if (isnull == 1) {
			$(this).addClass("form_null");
			null_num++;
		}
	});
	if (null_num == 0) {
		return true;
	} else {
		return false;
	}
}

//remove null style
$(".ajax_input input").click(function(){
	$(this).removeClass("form_null");
	$(this).parent().removeClass("form_null");
});


//ajax input submit
$(".ajax_submit").click(function(){
	var btn_obj = $(this);
	var form_obj = btn_obj.parents(".ajax_input");
	//find null
	if (form_obj.attr("nullable") == "set") {
		var find_text = "input[type=text][nullable]:not([sp]),div[type=divtext][nullable]";
	} else {
		var find_text = "input[type=text]:not([nullable]):not([sp]),div[type=divtext]:not([nullable])";
	}
	var null_num = 0;
	form_obj.find(find_text).each(function(){
		var isnull = 0;
		if ($(this).children("input[type=hidden]").length > 0) {
			if ($(this).children("input[type=hidden]").attr("value").length == 0) {
				isnull = 1;
			}
		} else if ($(this).val().length == 0 && $(this).find("input[type=checkbox]").length == 0) {
			isnull = 1;
		}
		if (isnull == 1) {
			$(this).addClass("form_null");
			null_num++;
		}
	});
	//find data
	if (null_num == 0) {
		var postdata = {};
		postdata["model"] = form_obj.attr("model");
		if (form_obj.attr("data") == "set") {
			var find_text = "[data=1]";
		} else {
			var find_text = "input[name][data!=0],select[name][data!=0],radio[checked][data!=0]";
		}
		form_obj.find(find_text).each(function(){
			if (postdata[$(this).attr("name")] == undefined) {
				postdata[$(this).attr("name")] = $(this).val();
				if ($(this).attr("type") == "checkbox") {
					postdata[$(this).attr("name")] = "{"+postdata[$(this).attr("name")]+"}";
				}
				if ($(this).hasClass("form_date") && postdata[$(this).attr("name")].length == 0) {
					postdata[$(this).attr("name")] = "null";
				}
			} else {
				if (String(postdata[$(this).attr("name")]).substr(0,1) != "{") {
					postdata[$(this).attr("name")] = "{"+postdata[$(this).attr("name")]+"}";
				}
				postdata[$(this).attr("name")] += "{"+$(this).val()+"}";
			}
			
		});
		if (form_obj.attr("for_id") == undefined) {
			postdata["insert"] = 1;
		} else {
			postdata["update"] = 1;
			postdata["for_id"] = form_obj.attr("for_id");
		}
		postdata["_method"] = "PUT";
		$.post("/console/model_ajax", postdata, function(data){
			if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
				alert_flavr("操作失败！错误信息："+data);
			}
			eval('var rdata = '+data);
			//alert(rdata.suc); 
			if (Number(rdata.suc) == 1) {
				alert_flavr(rdata.msg);
				$('#example').DataTable().draw();
				if (form_obj.attr("for_id") != undefined) {
					form_obj.find("input,select").attr("disabled","true");
					form_obj.find("div[type=divtext]").addClass("disabled");
					form_obj.find("div[type=divtext]").find(".glyphicon-remove").remove();
				} else if (form_obj.attr("clear") == "set") {
					form_obj.find("input[clear=1]").val("");
					form_obj.find("span[clear=1]").html("");
					form_obj.find("div[clear=1]").find("input[type=checkbox]").remove();
				} else {
					form_obj.find("input[type=text][clear!=0]").val("");//clear="all" or not set clear
					form_obj.find("input.real_data[clear!=0]").val("");
					form_obj.find(".real_show[clear!=0]").html("");
					form_obj.find("div[type=divtext][clear!=0]").find("input[type=checkbox]").parent("span").remove();//clear="all" or not set clear
				}
				form_obj.find("input[type!=hidden],div[type=divtext]").removeClass("form_null");
				
			} else {		
				alert_flavr(rdata.msg);
			}
		});	
	}
});

$(".flexible_form #btn-pass").click(function(){
	
	var btn_obj = $(this);
	var form_obj = btn_obj.parents(".flexible_form");

	var find_text = "input[type=text]:not([nullable]):not([sp]),div[type=divtext]:not([nullable])";
	var null_num = 0;
	form_obj.find(find_text).each(function(){
		var isnull = 0;
		if ($(this).children("input[type=hidden]").length > 0) {
			if ($(this).children("input[type=hidden]").attr("value").length == 0) {
				isnull = 1;
			}
		} else if ($(this).val().length == 0 && $(this).find("input[type=checkbox]").length == 0) {
			isnull = 1;
		}
		if (isnull == 1) {
			$(this).addClass("form_null");
			null_num++;
		}
	});
	if (null_num == 0) {
		var find_text = "input[name][data!=0],select[name][data!=0],radio[checked][data!=0]";
		//create form data
		var postdata = {};
		form_obj.find(find_text).each(function(){
			if (postdata[$(this).attr("name")] == undefined) {
				postdata[$(this).attr("name")] = {
					model:$(this).attr("model")==undefined?$("input[for='"+$(this).attr("name")+"']").attr("model"):$(this).attr("model"),
					col:$(this).attr("col")==undefined?$("input[for='"+$(this).attr("name")+"']").attr("col"):$(this).attr("col"),
					id:$(this).attr("id_in_model")==undefined?$("input[for='"+$(this).attr("name")+"']").attr("id_in_model"):$(this).attr("id_in_model"),
					value:$(this).val()
				}
				if ($(this).attr("type") == "checkbox") {
					postdata[$(this).attr("name")].value = "{"+postdata[$(this).attr("name")].value+"}";
				}
				if ($(this).hasClass("form_date") && postdata[$(this).attr("name")].length == 0) {
					postdata[$(this).attr("name")].value = "null";
				}
			} else {
				if (String(postdata[$(this).attr("name")].value).substr(0,1) != "{") {
					postdata[$(this).attr("name")].value = "{"+postdata[$(this).attr("name")].value+"}";
				}
				postdata[$(this).attr("name")].value += "{"+$(this).val()+"}";
			}
			
		});
		postdata["_method"] = "PUT";
		postdata["_token"] = $("#_token").attr("value");
		$.post("/console/flexible_ajax", postdata, function(data){
			if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
				alert_flavr("操作失败！错误信息："+data);
				return false;
			}
			eval('var rdata = '+data);
			//alert(rdata.suc); 
			if (Number(rdata.suc) == 1) {
				alert_flavr(rdata.msg,function(){
					window.location.reload();
				});
				//form_obj.find("input,select").attr("disabled","true");
				//form_obj.find("div[type=divtext]").addClass("disabled");
				//form_obj.find("div[type=divtext]").find(".glyphicon-remove").remove();
				
			} else {		
				alert_flavr(rdata.msg);
			}
		});	
	}
});

$(".proc_form #btn-pass").click(function(){
	
	var btn_obj = $(this);
	var form_obj = btn_obj.parents(".proc_form");

	if (valid_null(form_obj)) {
		//create form data
		var postdata = {};
		postdata["pdi_comment"] = form_obj.find("input[name='pdi_comment']").val();
		postdata["owner"] = new Array();
		form_obj.find("input[type='hidden'][name^='owner']").each(function(){
			var value = $(this).val().length==0?"0":$(this).val();
			postdata["owner"].push(value);
		});
		postdata["proc_id"] = form_obj.attr("proc_id");
		postdata["_method"] = "PUT";
		postdata["_token"] = $("#_token").attr("value");
		//console.log(postdata["owner"]);
		if (postdata["owner"].length == 0) {
			postdata["owner"] = "finish";
		}
		if (postdata["owner"] != "finish" || confirm("这是流程的最后一个环节，确认后任务将生效，并关闭流程")) {
			$.post("/console/procedure_pass", postdata, function(data){
				if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
					alert_flavr("操作失败！错误信息："+data);
					return false;
				}
				eval('var rdata = '+data);
				//alert(rdata.suc); 
				if (Number(rdata.suc) == 1) {
					alert_flavr(rdata.msg,function(){
						window.location.reload();
					});
					//form_obj.find("input,select").attr("disabled","true");
					//form_obj.find("div[type=divtext]").addClass("disabled");
					//form_obj.find("div[type=divtext]").find(".glyphicon-remove").remove();
					
				} else {		
					alert_flavr(rdata.msg);
				}
			});
		}
	}
});

$(".proc_form #btn-rollback").click(function(){
	var btn_obj = $(this);
	var form_obj = btn_obj.parents(".proc_form");
	if (form_obj.children(".proc_body:first").find("input[name='pdi_comment']").length > 0) {
		var confirm_text = "该环节为流程的第一个环节，退回将会删除流程，是否确定?";
	} else {
		var confirm_text = "确定要退回该流程？";
	}
	if (confirm(confirm_text)) {
		$.post("/console/procedure_rollback", {proc_id:form_obj.attr("proc_id"),pdi_comment:form_obj.find("input[name='pdi_comment']").val(),_token:$("#_token").attr("value"),_method:"PUT"}, function(data){
			if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){alert(data);}
			eval('var rdata = '+data);
			//alert(rdata.suc); 
			if (Number(rdata.suc) == 1) {
				alert_flavr(rdata.msg,function(){
					window.location.reload();
				});
			} else {		
				alert_flavr(rdata.msg);
			}
		});
	}
});

$('.form_date').datetimepicker({
    language:  'zh-CN',
    weekStart: 1,
	autoclose: 1,
	todayHighlight: 1,
	todayBtn: 1,
	minView: 2,
	startView: 2,
    showMeridian: 1
});
$('.form_date').css("background-color","white");
$('.form_date').wrap("<div style=\"position:relative\"></div>");
$('.form_date').after("<span class=\"glyphicon glyphicon-trash\" style=\"position:absolute;top:33%;right:7px;\" onclick=\"$(this).parent('div').children('input').val('');\"></span>");



$("input[bind]:not([multiples]):not([refer]):not([readonly]):not([disabled])").intelligent_input({
	force: 1,
});

$("input[bind][multiples]:not([readonly]):not([disabled])").intelligent_input({
	force: 1,
	multiple: 1,
});

$("input[history]:not([bind]):not([multiples]):not([readonly]):not([disabled])").intelligent_input();
$("input[bind][refer]").intelligent_input({
	force: 0 
});

//console.log($.fn);

//$(".panel_nav_item span").prepend("");