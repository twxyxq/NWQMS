//$(".ajax_input").ready(function(){
	//if ($(this).attr("for_id") != undefined) {

	//}
//});
function valid_null(form_obj){
	var find_text = "input[type=text]:not([nullable]):not([sp]),div[type=divtext]:not([nullable])";
	var null_num = 0;
	form_obj.find(find_text).each(function(){
		//alert($(this).attr("name"));
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

function get_form_data(form_obj){
	var postdata = {};
	postdata["model"] = form_obj.attr("model");
	if (form_obj.attr("data") == "set") {
		var find_text = "[data=1]";
	} else {
		var find_text = "input[name][data!=0],select[name][data!=0],radio[checked][data!=0],textarea[data!=0]";
	}
	form_obj.find(find_text).each(function(){
		var tt = $(this);
		if (typeof(tt.attr("text_null")) == "undefined") {
			clear_input_blank(tt);
		}
		if (postdata[tt.attr("name")] == undefined) {
			postdata[tt.attr("name")] = tt.val();
			if (tt.attr("type") == "checkbox") {
				postdata[tt.attr("name")] = "{"+postdata[tt.attr("name")]+"}";
			}
			if ((tt.hasClass("form_date") || tt.attr("nullable") != undefined) && postdata[tt.attr("name")].length == 0) {
				postdata[tt.attr("name")] = "null";
			}
		} else {
			if (String(postdata[tt.attr("name")]).substr(0,1) != "{") {
				postdata[tt.attr("name")] = "{"+postdata[tt.attr("name")]+"}";
			}
			postdata[tt.attr("name")] += "{"+tt.val()+"}";
		}
		
	});
	if (form_obj.attr("for_id") == undefined) {
		postdata["insert"] = 1;
	} else {
		postdata["update"] = 1;
		postdata["for_id"] = form_obj.attr("for_id");
	}
	postdata["_method"] = "PUT";
	postdata["_token"] = $("#_token").attr("value");
	return postdata;
}

//发送计算请求并返回
function trigger_cal(key){
	//禁止提交按钮
	disable_submit();
	
	var form_obj = $("[name="+key+"]").parents(".ajax_input");
	var postdata = get_form_data(form_obj);
	postdata["cal_para"] = key;
	ajax_post("/console/model_cal", postdata, function(data){
		if (Number(data.suc) == 1) {
			//后续是否有执行函数的标志
			var next_exec = 0;
			//计算成功，结果写入相应的文本框
			for(var r in data.result){
				if ($("[name="+r+"][readonly]").length > 0 || ($("#"+r+"_cal").length > 0 && $("#"+r+"_cal").is(":checked")) || ($("[name="+r+"]").attr("is_cal") != undefined && Number($("[name="+$("[name="+r+"]").attr("is_cal")+"]").val()) == 0)) {
					$("[name="+r+"]").val(data.result[r]);

					if ($("[name="+r+"]").attr("onchange") != undefined || $("[name="+r+"]").attr("change_fn") != undefined || $("[name="+r+"]").attr("blur_cal") != undefined) {
						//有后续执行函数
						next_exec = 1;
						trigger_cal(r);
					}

					$("[name="+r+"]").addClass("input_emphasize");
					setTimeout('$("[name='+r+']").removeClass("input_emphasize")',1000);
				}
			}
			if (next_exec == 0) {
				//没有执行函数，则恢复提交按钮
				recover_submit();
			}
		} else {
			alert_flavr(data.msg);
			//恢复提交按钮
			recover_submit();
		}
	});	
}

//发送列名和数值进行有效性验证
function model_valid(key,value,id,fn){
	id = typeof(id)=="undefined"?0:id;
	fn = typeof(fn)=="undefined"?"":fn;
	var postdata = {};
	if ($("[name="+key+"]").attr("model") != undefined) {
		postdata["model"] = $("[name="+key+"]").attr("model");
		if ($("[name="+key+"]").attr("for_id") != undefined) {
			postdata["id"] = $("[name="+key+"]").attr("for_id");
		}
	} else {
		var form_obj = $("[name="+key+"]").parents(".ajax_input");
		postdata["model"] = form_obj.attr("model");
		if (form_obj.attr("for_id") != undefined) {
			postdata["id"] = form_obj.attr("for_id");
		}
	}
	postdata["valid_col"] = key;
	postdata["valid_value"] = value;
	postdata["_method"] = "PUT";
	postdata["_token"] = $("#_token").attr("value");
	ajax_post("/console/model_valid", postdata, function(data){
		if (Number(data.suc) == 1) {
			if (fn != "") {
				//如有后续执行的函数，则执行
				eval(fn);
			} else {
				//如果没有后续执行，恢复提交按钮
				recover_submit();
			}
			$("[name="+key+"]").addClass("input_success");
			setTimeout('$("[name='+key+']").removeClass("input_success")',1000);
		} else {
			//恢复原值
			$("[name="+key+"]").val(data.origin);
			//强调错误位置
			$("[name="+key+"]").addClass("input_emphasize");

			//恢复提交按钮
			recover_submit();

			setTimeout('$("[name='+key+']").removeClass("input_emphasize")',1000);
			//提示错误信息
			alert_append($("[name="+key+"]"),data.msg,1000);
		}
	});	
}

//失去焦点运行
function blur_fn(this_name){
	var this_input = $("[name="+this_name+"]");
	//如果还在focus则不允许,避免选择过程中失去焦点
	if (!this_input.is(":focus")) {
		var a = 0;
		//需验证+1
		if (this_input.attr("blur_valid") != undefined) {
			a += 1;
		}
		//需计算+2
		if (this_input.attr("blur_cal") != undefined) {
			a += 2;
		}
		switch(a){
			//1：只进行验证
			case 1:model_valid(this_input.attr("name"),this_input.val());break;
			//2：只进行计算
			case 2:trigger_cal(this_name);break;
			//3：验证后进行计算
			case 3:model_valid(this_input.attr("name"),this_input.val(),0,"trigger_cal('"+this_name+"')");break;
			default:;
		}
	}
}

//是否进行计算切换
function cal_switch(select_text,key){
	var items = select_text.split(",");
	var selector = "";
	for(var i in items){
		selector += ",[name="+items[i]+"]";
	}
	selector = selector.substr(1);
	//console.log(selector);
	if (Number($("[name="+key+"]").val()) == 1) {
		$(selector).removeClass("disabled");
	} else {
		$(selector).addClass("disabled");
	}
	
}




//remove null style
$(".ajax_input input").click(function(){
	$(this).removeClass("form_null");
	$(this).parent().removeClass("form_null");
});
/*
$(".ajax_input input").on("focus",function(){
	$(".ajax_submit").html("录入中……");
	$(".ajax_submit").attr("disabled",true);
});
$(".ajax_input input").on("blur",function(){
	setTimeout(function(){
		$(".ajax_submit").html("录入");
		$(".ajax_submit").attr("disabled",false);
	},1000);
	
});
*/


//ajax input submit
$(".ajax_submit").click(function(){
	var btn_obj = $(this);
	var form_obj = btn_obj.parents(".ajax_input");
	//find null
	if (form_obj.attr("nullable") == "set") {
		var find_text = "input[type=text][nullable]:not([sp]),textarea[nullable]:not([sp]),div[type=divtext][nullable]";
	} else {
		var find_text = "input[type=text]:not([nullable]):not([sp]),textarea:not([nullable]),div[type=divtext]:not([nullable])";
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
		$.post("/console/model_ajax", get_form_data(form_obj), function(data){
			if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
				alert_flavr("操作失败！错误信息："+data);
			}
			eval('var rdata = '+data);
			//alert(rdata.suc); 
			if (Number(rdata.suc) == 1) {
				//变更时执行函数，否则只提示
				if (rdata.dirty != undefined) {
					alert_flavr(rdata.msg,function(){
						ajax_post("/console/alt_confirm",{"model":rdata.model,"id":rdata.id,"dirty":rdata.dirty,"original":rdata.original},function(data){
							if (data.suc == 1) {
								if (window.parent.flavr != undefined || window.parent.flavr != null) {
									window.parent.flavr.close();
								}
								window.parent.dt_alt_proc(data.proc_id,rdata.model,rdata.id);

							}
							alert_flavr(data.msg);
						});
					});
				} else {
					alert_flavr(rdata.msg);
				}
				$('#example').DataTable().draw();
				if (form_obj.attr("for_id") != undefined) {
					form_obj.find("input,select").attr("disabled","true");
					form_obj.find("div[type=divtext]").addClass("disabled");
					form_obj.find("div[type=divtext]").find(".glyphicon-remove").remove();
					form_obj.find(".ajax_submit").css("display","none");
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

var ii_select = null;

var ii_multiple = null;

function form_init(){
	$('.form_date').each(function(){
		$(this).datetimepicker({
		    language:  'zh-CN',
		    weekStart: 1,
			autoclose: 1,
			todayHighlight: 1,
			todayBtn: 1,
			minView: $(this).attr("minView")==undefined?2:$(this).attr("minView"),
			startView: $(this).attr("startView")==undefined?2:$(this).attr("startView"),
			startDate: $(this).attr("startDate")==undefined?"":$(this).attr("startDate"),
			endDate: $(this).attr("endDate")==undefined?"":$(this).attr("endDate"),
		    showMeridian: 1
		});
	});
	//$('.form_date[startdate]').each(function(){
		//var t = $(this);
		//t.datetimepicker('setStartDate', t.attr("startdate"));
	//})
	$('.form_date').css("background-color","white");
	$('.form_date').wrap("<div style=\"position:relative\"></div>");
	$('.form_date').after("<span class=\"glyphicon glyphicon-trash\" style=\"position:absolute;top:33%;right:7px;\" onclick=\"$(this).parent('div').children('input').val('');\"></span>");



	ii_select = $("input[bind]:not([multiples]):not([refer]):not([readonly]):not([disabled])").intelligent_input({
		force: 1,
	});

	ii_multiple = $("input[bind][multiples]:not([readonly]):not([disabled])").intelligent_input({
		force: 1,
		multiple: 1,
	});

	$("input[history]:not([bind]):not([multiples]):not([readonly]):not([disabled])").intelligent_input();
	$("input[bind][refer]").intelligent_input({
		force: 0 
	});

	//自动保存的input
	$("input[autosave]").on("change",function(){
		var o = $(this);
		if (o.attr("autosave").substr(0,1) == "{") {
			eval("var save_data = "+o.attr("autosave")+";");
			var postdata = {};
			postdata["update"] = 1;
			postdata["model"] = save_data["model"];
			postdata["for_id"] = save_data["id"];
			postdata[save_data["col"]] = o.val();
			if (save_data["_auth"] != undefined) {
				postdata["_auth"] = save_data["_auth"];
			}
			ajax_post("/console/model_ajax",postdata,function(data){
				if (data.suc == 1) {
					o.css("background-color","lightgreen");
				} else {
					o.css("background-color","pink");
				}
				setTimeout(function(){
					o.css("background-color","");
				},1000);
			});
		}
	});



	//带有blurfn的focus会禁止提交,blur会触发计算
	$("input[blurfn]").focus(function(){
		disable_submit();
	});
	$("input[blurfn]").blur(function(){
		var this_name = $(this).attr("name");
		setTimeout("blur_fn('"+this_name+"')",380);
	});
}
form_init();

//禁止提交按钮
function disable_submit(){
	$(".ajax_submit").html("录入中……");
	$(".ajax_submit").attr("disabled",true);
}
//恢复提交按钮
function recover_submit(){
	$(".ajax_submit").html($(".ajax_submit").attr("title"));
	$(".ajax_submit").attr("disabled",false);
}


$("input[autopost]").on("keyup",function(e){
	var url = $(this).attr("autopost");
	var value = $(this).val();
	var fn = $(this).attr("autopost_fn");
	var keycode = e.which;
	if (keycode == 13) {
		if ($(".flavr-container").length == 0) {
			ajax_post(url,{"code":value},fn);
		} else {
			$(".flavr-container").remove();
		}
	}
});
//console.log($.fn);

//$(".panel_nav_item span").prepend("");