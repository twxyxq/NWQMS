//屏蔽后退键
		function pingbi(id){
		    var k = window.event.keyCode;
			if (k == id){window.event.keyCode = 0; window.event.returnValue = false; return false;}
		}


//打印相关
		var LODOP; //声明为全局变量
		function print_array(vj_array){			
			LODOP = getLodop(document.getElementById('LODOP_OB'),document.getElementById('LODOP_EM')); 
		    LODOP.PRINT_INIT("记录单");				
			for(var i = 0; i < vj_array.length; i++){				
				if(i > 0){					
					LODOP.NewPage();				
				}				
			LODOP.ADD_PRINT_HTM(38,46,672,1000,document.getElementById("tip"+vj_array[i]).innerHTML);
		    }
		    LODOP.PREVIEW();		
		}
		
		function print_class(class_name){
		    LODOP = getLodop(); 
		    LODOP.PRINT_INIT("记录单");	
            var i = 0;			
			$("."+class_name).each(function(){
			    if(i > 0){					
					LODOP.NewPage();				
				}				
			    LODOP.ADD_PRINT_HTM(50,50,673,1000,document.getElementById($(this).attr("id")).innerHTML);
				$.post("/wj/print/ajax_wj_detail.php", {tsk_id:""+$(this).attr("id")+""}, function(data){
					//if (Number(data) > 0){
						//alert("这是第"+data+"次打印该记录单");
					//} else {
						//alert("打印记录写入失败！");
					//}
				});
				i++;
			});
		    LODOP.PREVIEW();
		}
		
		function print_object(object_name){
		    LODOP = getLodop(); 
		    LODOP.PRINT_INIT("记录单");	
            var i = 0;			
			$(object_name).each(function(){
			    if(i > 0){					
					LODOP.NewPage();				
				}				
			    LODOP.ADD_PRINT_HTM(50,50,673,1000,$(this).html());
				i++;
			});
		    LODOP.PREVIEW();
		}

		

		function blank_clear_and_return_value(ss){
		    while(ss.lastIndexOf(" ")>=0){
		        ss = ss.replace(" ","")
		    }
			return ss;
		}

		

		function clear_input_blank(input_object){
		    var d = input_object.val();
			d = blank_clear_and_return_value(d);
		    input_object.val(d);
			return d;
		}

		//多选字符串“{}”与数组互转
		function multiple_to_array(text_input){
			if (text_input == null || text_input.length == 0) {
				return array();
			} else if (text_input.substr(0,1) == "{") {
				return text_input.substr(1,text_input.length-2).split("}{");
			} else {
				return array(text_input);
			}
		}
		function array_to_multiple(array_input){
			if (array_input.length == 0) {
				return "";
			}
			r_text = "";
			for(var n in array_input) {
				r_text += "}{"+array_input[n];
			}
			return r_text.substr(1)+"}";
		}

		function null_check(){
			var r = new Array();
			if (arguments[0] instanceof Array){
			    for(var i=0; i<arguments[0].length; i++){
			        if (clear_input_blank($("#"+arguments[0][i])).length == 0){
				        r.push(arguments[0][i]);
				    }
			    }
			} else if (typeof arguments[0] == "string"){
			    if (arguments[0].substr(0,1) == "."){
				    $(arguments[0]).each(function(){
					    if (clear_input_blank($(this)).length == 0){
				            r.push($(this).attr("id"));
				        }
					});
				} else {
				    for(var i=0; i<arguments.length; i++){
			            if (clear_input_blank($("#"+arguments[i])).length == 0){
				            r.push(arguments[i]);
				        }
			        }
				}
			}			
			return r;
		}

		

		function fill_na(value, item){
		    if(item == "class"){
		        $("."+value).each(function(){
				    if ($(this).attr("value") == ""){
					    $(this).attr("value", "N/A");
					}
				});
		    } else if (item == "id"){
		        if ($("#"+value).attr("value") == ""){
					$("#"+value).attr("value", "N/A");
				}
		    }
		}

		

		function clear_na(value, item){
		    if(item == "class"){
		        $("."+value).each(function(){
				    if ($(this).attr("value") == "N/A"){
					    $(this).attr("value", "");
					}
				});
		    } else if (item == "id"){
		        if ($("#"+value).attr("value") == "N/A"){
					$("#"+value).attr("value", "");
				}
		    }
		}

		

		

		function single_str(c){
		    if ((c >= 0x0001 && c <= 0x007e) || (c >= 0xff60 && c<= 0xff9f)){
				return true;
			} else {
				return false;
			}
		}

		function strlen(str){
		    var len = 0;
			for(var i = 0; i < str.length; i++){
			    var c = str.charCodeAt(i);
				if (single_str(c)){
				    len++;
				} else {
				    len += 2;
				}
			}
			return len;
		}

		function strlen_rst(str, nn){
		    var len = 0;
		    for(var i = 0; i < str.length && len < nn; i++){
			    var c = str.charCodeAt(i);
				if (single_str(c)){
				    len++;
				} else {
				    len += 2;
				}
			}
			if (i == str.length && len <= nn){
			    return 0;
			} else {
			    return i;
			}
		}		

		

		function null_tip(object){
			if(clear_input_blank(object).length == 0){
			    object.css("background-color","yellow");
			}
		}



	


		function get_model_data(obj){
			var def = {
				model: "",
				col: "",
				where: "",
				search: "",
				limit: 10,
				groupby: "",
				fn: function(){},
				para: [],
				_token: $("#_token").attr("value")
			};
			$.extend(def, obj);
			var fn = def.fn;
			var para = def.para;
			delete def.fn;
			delete def.para;
			$.post("/console/get_model_data", def, function(data){
				if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
					alert("操作失败！错误信息："+data);
				}
				eval('var rdata = '+data);
				//alert(rdata.suc); 
				if (Number(rdata.suc) == 1) {
					para.unshift(rdata.data);
					fn.apply(this,para);
				} else {		
					alert(rdata.msg);
				}
			});	
		}

		function get_bind(obj){
			var def = {
				model: "",
				col: "",
				type: "",
				search: "",
				refer: 0,
				limit: 10,
				group: 1,
				fn: function(){},
				fail_fn: function(){},
				para: [],
				_token: $("#_token").attr("value"),
				_method: "PUT"
			};
			$.extend(def, obj);
			var fn = def.fn;
			var fail_fn = def.fail_fn;
			var para = def.para;
			delete def.fn;
			delete def.fail_fn;
			delete def.para;
			$.post("/console/get_bind", def, function(data){
				if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
					alert("操作失败！错误信息："+data);
				}
				eval('var rdata = '+data);
				//alert(rdata.suc); 
				if (Number(rdata.suc) == 1) {
					para.unshift(rdata.data);
					fn.apply(this,para);
				} else {		
					//alert(rdata.msg);
					fail_fn.apply(this,para);
				}
			});	
		}

		function is_bind(obj){
			var def = {
				model: "",
				col: "",
				type: "",
				value: "",
				fn: function(){},
				fail_fn: function(){},
				para: [],
				_token: $("#_token").attr("value")
			};
			$.extend(def, obj);
			var fn = def.fn;
			var fail_fn = def.fail_fn;
			var para = def.para;
			delete def.fn;
			delete def.fail_fn;
			delete def.para;
			$.post("/console/is_bind", def, function(data){
				if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
					alert("操作失败！错误信息："+data);
				}
				eval('var rdata = '+data);
				//alert(rdata.suc); 
				if (Number(rdata.suc) == 1) {
					para.unshift(rdata.data);
					fn.apply(this,para);
				} else {		
					//alert(rdata.msg);
					fail_fn.apply(this,para);
				}
			});	
		}


		function table_flavr(url,title,button,onClose){
			button = typeof(button)=="undefined"?"":button;
			onClose = typeof(onClose)=="undefined"?"":onClose;
			new_flavr(url,title,button,function(){
				$("#example").DataTable().draw(false);
			});
		}

		var flavr = null;
		var a_flavr = null;

		function new_flavr(url,title,button,onClose){
			button = typeof(button)=="undefined"?"":button;
			onClose = typeof(onClose)=="undefined"?"":onClose;
			var max = 968;
			var current = $("body").width()*0.95;
			var min = 320;
			if (current > max) {
				current = max;
			}
			if (current < min) {
				current = min;
			}
			if ($(window).height() > 400) {
				var winheight = $(window).height()-130;
			} else {
				var winheight = $(window).height()-110;
			}
			var outerdiv = winheight+20;
			if (button == "") {
				button = {
			      	close   : { text: '关闭' }
			    };
			}
			if (typeof(button) == "function" && onClose == ""){
			    onClose = button;
				button = {
			      	close   : { text: '关闭' }
			    };
			} else if (typeof(button) == "function" && typeof(onClose) == "function"){
				button = {
			      	close   : { text: '关闭', action: button }
			    };
			} else if (onClose == "") {
				onClose = function(){

				};
			}
			flavr = new $.flavr({
				title       : title,
				position	: 'top-mid',
				closeOverlay : true,
				closeEsc     : true,
			    content     : '<div style="max-height:'+outerdiv+'px;-webkit-overflow-srolling:touch;overflow-y:auto;"><iframe id="current_iframe" width="'+current+'px" height="'+winheight+'px" src="'+url+'" frameborder="0" allowfullscreen></iframe></div>',
			    buttons     : button,
			    onClose		: onClose
			});
		}

		function alert_flavr(msg,fn){
			fn = typeof(fn)=="undefined"?"":fn;
			if (fn == "") {
				a_flavr = new $.flavr(msg.toString());
			} else {
				a_flavr = new $.flavr({
					content: msg,
					onClose: fn
				});
			}
			
		}

		function alert_append(obj,msg,timeout){
			timeout = typeof(timeout)=="undefined"?0:timeout;
			var id = Math.random().toString().substr(2);
			var html = "<div id='alert"+id+"' style='position:absolute;top:-12px;right:0px;height:11px;background-color:lightyellow;font-size:10px;color:red;white-space: nowrap;overflow:visible;' onclick='$(this).remove()'>"+msg+"</div>";
			obj.after(html);
			if (timeout > 0) {
				setTimeout("$('#alert"+id+"').remove();",timeout);
			}
		}

		function detail_flavr(url,title,id){
			new_flavr(url+'?id='+id,title);
		}

		function dt_edit(model,id,para){
			para = typeof(para)=="undefined"?"":para;
			new_flavr("/console/dt_edit?model="+model+"&id="+id+"&para="+para,"编辑",function(){
				$('#example').DataTable().draw(false);
			});
		}

		function dt_alt_info(model,id,para){
			para = typeof(para)=="undefined"?"":para;
			table_flavr("/console/dt_alt_info?model="+model+"&id="+id+"&para="+para,"信息变更");
		}

		function dt_status_proc(proc_id,model,id,para,title){
			para = typeof(para)=="undefined"?"":para;
			title = typeof(title)=="undefined"?"审核":title;
			dt_proc("status_avail_procedure",proc_id,model,id,para,title);
		}
		function dt_alt_proc(proc_id,model,id,para,title){
			para = typeof(para)=="undefined"?"":para;
			title = typeof(title)=="undefined"?"审核":title;
			dt_proc("alt_procedure",proc_id,model,id,para,title);
		}
		function dt_alt_pressure_test_proc(proc_id,model,id,para,title){
			para = typeof(para)=="undefined"?"":para;
			title = typeof(title)=="undefined"?"审核":title;
			dt_proc("alt_pressure_test_procedure",proc_id,model,id,para,title);
		}
		function dt_alt_exam_specify_proc(proc_id,model,id,para,title){
			para = typeof(para)=="undefined"?"":para;
			title = typeof(title)=="undefined"?"审核":title;
			dt_proc("alt_exam_specify_procedure",proc_id,model,id,para,title);
		}

		function dt_proc(pd_class,proc_id,model,id,para,title,first_page){
			para = typeof(para)=="undefined"?"":para;
			title = typeof(title)=="undefined"?"审核":title;
			first_page = typeof(first_page)=="undefined"?"default":first_page;
			if ($.isArray(id)) {
				id = array_to_multiple(id);
			}
			var detail_page = "/console/procedure_info?pd_class="+pd_class+"&proc_id="+proc_id+"&model="+model+"&id="+id+"&para="+para;
			var check_page = "/console/view_procedure?proc="+pd_class+"&proc_id="+proc_id;
			table_flavr(first_page=="default"?detail_page:check_page,title,{
				info    : {
                    style   : "Primary",
                    text    : "详情",
                    action  : function(){
                    	$("#current_iframe").attr("src",detail_page);
                        return false;
                    }
                },
				pass	: {
					style	: 'success',
					text	: '审批',
					action	: function(){
						if (proc_id > 0) {
							//$("#current_iframe").attr("src","/console/view_procedure?proc="+pd_class+"&model="+model+"&id="+id+"&proc_id="+proc_id);
							$("#current_iframe").attr("src",check_page);
						} else {
							if (confirm("该流程尚未启动，是否启动流程？")) {
								ajax_post("/console/procedure_create", {"model":model,"id":id}, function(data){
									if (data.suc == 1) {
										alert_flavr(data.msg,function(){
											window.parent.flavr.close();
											dt_proc(pd_class,data.proc_id,model,id,para,title,"check");
											//$("#current_iframe").attr("src","/console/view_procedure?proc="+pd_class+"&proc_id="+data.proc_id);
										});
									} else {		
										alert_flavr(data.msg);
									}
								});
							}
						}
						return false;
					}
				},
                close   : {
                    text    : '关闭'
                }
			});
		}


		function dt_delete(model,id){
			var def = {
				model: model,
				id: id,
				delete: 1,
				_token: $("#_token").attr("value"),
				_method: "PUT"
			};
			if (confirm("确认删除？")) {
				$.post("/console/model_ajax", def, function(data){
					if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){alert(data);}
					eval('var rdata = '+data);
					//alert(rdata.suc); 
					if (Number(rdata.suc) == 1) {
						alert_flavr(rdata.msg);
						$('#example').DataTable().draw(false);
					} else {		
						alert_flavr(rdata.msg);
					}
				});	
			}	
		}
		//通用流程开启
		function dt_proc_create(proc,model,ids,pd_name,confirm_msg){
			confirm_msg = typeof(confirm_msg)=="undefined"?"确定开启流程？":confirm_msg;
			if (confirm(confirm_msg)) {
				var postdata = {};
				postdata["proc"] = proc;
				postdata["model"] = model;
				postdata["id"] = ids;
				postdata["proc"] = proc;
				if (typeof(pd_name) != "undefined") {
					postdata["pd_name"] = pd_name;
				}
				ajax_post("/console/procedure_create",postdata,function(data){
					if (data.suc == 1) {
						dt_proc(proc,data.proc_id,model,data.id);
					} else {
						alert_flavr(data.msg);
					}
				});
			}
			
		}

		function dt_r(id){
			if (confirm("确定返修？")) {
				ajax_post("/wj/wj_r_exec",{"id" : id},function(data){
					if (data.suc == 1) {
						dt_proc("status_avail_procedure",data.proc_id,"wj",data.wj_id);
						/*
						table_flavr('/console/view_procedure?proc=status_avail_procedure&proc_id='+data.proc_id,"焊口生效流程",{
	                        info    : {
	                            style   : 'Primary',
	                            text    : '焊口详情',
	                            action  : function(){
	                                $('#current_iframe').attr('src','/console/dt_edit?model=wj&id='+id);
	                                return false;
	                            }
	                        },
	                        pass    : {
	                            style   : 'success',
	                            text    : '审批',
	                            action  : function(){
	                                $('#current_iframe').attr('src','/console/view_procedure?proc=status_avail_procedure&proc_id='+data.proc_id);
	                                return false;
	                            }
	                        },
	                        close   : {
	                            text    : '关闭'
	                        }
	                    });
	                    */
					} else {
						alert_flavr(data.msg);
					}
				});
			}
		}

		function dt_model(id){
			$(".ajax_input").find("input[name][name!=_token][data!=0]:not([only]),select[name][data!=0],radio[checked][data!=0]").each(function(){
				$(this).val($("#"+$(this).attr("name")+"_"+id).html());
				if($(this).attr("type") == "hidden" && $("#sp_"+$(this).attr("name")).length > 0){
					$("#sp_"+$(this).attr("name")).attr("refresh",1);
					$("#sp_"+$(this).attr("name")).val($("#"+$(this).attr("name")+"_"+id).html());
					$("#sp_"+$(this).attr("name")).trigger("dblclick");
				}
			});
			$(".ajax_input").find("input[name][for][multiples][data=0]").each(function(){
				$(this).val($("#"+$(this).attr("for")+"_"+id).html());
				if($(this).val().length > 0){
					$(this).attr("refresh",1);
					$("#base_"+$(this).attr("for")+" input[type=checkbox]").parent("span").remove();
					$(this).trigger("dblclick");
				}
			});
		}

		function dt_version_update(model,id,para){
			para = typeof(para)=="undefined"?"":para;
			var html =  
			'   <div>' +
			'       <input type="hidden" name="proc_path[] value="编写">编写 ' +
			'       <input type="text" class="proc_form form-control" name="proc_user_1" bind="{model:\'User\',col:\'id\',show:\'CONCAT(code,name)\'}" />' +
			'   </div>' +
			'   <div>' +
			'       <input type="hidden" name="proc_path[] value="审核">审核 ' +
			'       <input type="text" class="proc_form form-control" name="proc_user_2" bind="{model:\'User\',col:\'id\',show:\'CONCAT(code,name)\'}" />' +
			'   </div>' +
			'   <div>' +
			'       <input type="hidden" name="proc_path[] value="批准">批准 ' +
			'       <input type="text" class="proc_form form-control" name="proc_user_3" bind="{model:\'User\',col:\'id\',show:\'CONCAT(code,name)\'}" />' +
			'   </div>';
			new $.flavr({
				title       : '流程设置',
			    content: html
			});
			$(".proc_form").intelligent_input({
				force: 1
			});
		}

		function version_update(model,id){
			if (confirm("确定要升版吗？")) {
				var def = {
					model: model,
					id: id,
					_token: $("#_token").attr("value"),
					_method: "PUT"
				};
				$.post("/console/version_update", def, function(data){
					if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
						alert_flavr("操作失败！错误信息："+data);
						return false;
					}
					eval('var rdata = '+data);
					//alert(rdata.suc); 
					if (Number(rdata.suc) == 1) {
						alert_flavr(rdata.msg);
						$('#example').DataTable().draw(false);
					} else {
						alert_flavr(rdata.msg);
					}
				});	
			}
		}

		function ajax_post(url,postdata,fn){
			if (postdata["_token"] == undefined) {
				postdata["_token"] = $("#_token").val();
			}
			if (postdata["_method"] == undefined) {
				postdata["_method"] = "PUT";
			}
			$.post(url, postdata, function(data){
				if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
					alert_flavr("操作失败！错误信息："+data);
				} else {
					eval('var rdata = '+data);
					fn(rdata);
				}
			});	
		}
		
