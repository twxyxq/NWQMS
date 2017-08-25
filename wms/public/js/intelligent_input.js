		//jquery

		(function($){

		    $.fn.intelligent_input = function(data_input){
				//drop_type下拉类型，mouse为鼠标右键下拉，input为输入框下拉
				var defaults = {
                    data_input: ['N/A'],
					db_source: ['N/A'],
					drop_type: 'input',		
					max_width: "95vw",//最大宽度
					min_width: -1,//最小宽度		
					null_tip: 0,//为0则不允许空格	
					max_len: 10,
					force:0,//force为1，强制为绑定的值
					multiple:false,//开启multiple，则可以多选值。注意：multiple的数量由input上的multiples决定，值“0,1”为不限制数量
					fn: undefined,
					type:"",
					model:".ajax_input",//确定在哪个父元素上取的model、only等值
					col:"",
					show:"",
					autosave:false //自动保存开关
                };


				if (data_input != undefined) {
					var opts = $.extend(defaults, data_input);
				} else {
					var opts = defaults;
				}
				
				
				
				//复制操作
				var copy_to_input = function(o, value){
				    $(o).val(value);
				}
				/*存储功能，已禁用
				var store_filter = function(o){
					$("#filter_"+$(o).attr("id")).html($(o).val());
				}
				var read_filter = function(o){
					$(o).val($("#filter_"+$(o).attr("id")).html());
				}
				*/

				

				var dropdown_menu_show = function(o, e){
					$(".it_dropdown").remove();
				    if (opts.drop_type != "mouse" || e.which == 3){
						if (opts.min_width == -1) {
							var min_width = $(o).width()+parseInt($(o).css("padding-left"))+parseInt($(o).css("padding-left"));
							//alert(min_width);
						} else {
							var min_width = opts.min_width;
						}
						var max_width = Math.max($(o).width()*1.5,$("body").width()*0.9);
						var html = "<div align='center' class='it_dropdown' id='drop"+$(o).attr("id")+"' style='z-index:99999;border: 1px solid grey;max-height: 550px;position:absolute;background:white;'>";
						html += "<table width='"+min_width+"' cellspacing='0' cellpadding='0' class='text_limit' style='max-width:"+opts.max_width+";border:1px solid grey;'>";

						//alert(1);
						//生成表单元素
						if (opts.drop_type == "mouse"){
							if (window.clipboardData) {  
								html += "<tr id='drop_paste"+$(o).attr("id")+"' style='background-color:aliceblue'><td align='center' title='"+window.clipboardData.getData("text")+"'>粘 &nbsp; 贴</td></tr>";
							} else { 
								html += "<tr id='drop_paste"+$(o).attr("id")+"' style='background-color:aliceblue'><td align='center' title='浏览器不支持该功能，粘贴请按Ctrl+V'>粘贴请按Ctrl+V</td></tr>";
							}
							html += "<tr id='drop_divide"+$(o).attr("id")+"' style='background-color:cadetblue;color:white'><td align='center'>推 荐 值</td></tr>";
						}
						html += "</table></div>";
						$("body").append(html);
						//$("#drop"+$(o).attr("id")).width($(o).width());
						if (opts.drop_type == "mouse"){
							$("#drop"+$(o).attr("id")).offset({ top: e.pageY+3, left: e.pageX+5 });
							$("#drop_paste"+$(o).attr("id")).live('click', function () {
								$(o).focus();
								if (window.clipboardData) {  
									$(o).val(window.clipboardData.getData("text"));
								}
								finalize($(o).attr("id"));
							});
						} else {
							$("#drop"+$(o).attr("id")).offset({ top: $(o).offset().top + $(o).height() + 6, left: $(o).offset().left });
						}

					    if (typeof($(o).attr("bind")) != "undefined"){
					    	if ($(o).attr("bind").substr(0,1) == "[") {
					    		eval("var data_bind = "+$(o).attr("bind")+";");
					    		if ($(o).val().length > 0) {
					    			var d_array = new Array;
					    			for (var i = 0; i < data_bind.length; i++) {
						    			if (Number(String(data_bind[i]).indexOf(String($(o).val()))) >= 0) {
						    				//alert(i);
						    				d_array.push(data_bind[i]);
						    			}
						    		}
					    		} else {
					    			var d_array = data_bind;
					    		}
					    		if (d_array.length > 0) {
					    			dropdown_menu(d_array,o,e);
					    		} else {
					    			none_dropdown(o,e);
					    		}
					    		
					    	} else {
					    		if ($(o).attr("bind") != undefined && $(o).attr("bind").substr(0,1) == "{") {
									eval("var data_info = "+$(o).attr("bind")+";");
									var model = data_info.model==undefined?"":data_info.model;
									var type = data_info.type==undefined?"":data_info.type;
									var col = data_info.col==undefined?"":data_info.col;
									var show = data_info.show==undefined?"1":data_info.show;
								} else {
					    			var model = $(o).parents(opts.model).attr("model");
						    		var col = $(o).attr("for")==undefined?$(o).attr("name"):$(o).attr("for");
						    		var type = $(o).parents(opts.model).attr("type");
						    		var show = "";
					    		}
					    		

								get_bind({
									model: model,
									col: col,
									type: type,
									show: show,
									search: $(o).val(),
									limit: opts.max_len,
									group: 1,
									fn: dropdown_menu,
									fail_fn: none_dropdown,
									para: [o,e]
								});
						   
					    	}					    
						    
						} else if (typeof($(o).attr("history")) != "undefined"){
							var model = $(o).parents(opts.model).attr("model");
				    		var col = $(o).attr("name");
					    	var type = $(o).parents(opts.model).attr("type");
	                        get_bind({
								model: model,
								col: col,
								type: type,
								search: $(o).val(),
								limit: opts.max_len,
								group: 1,
								fn: dropdown_menu,
								fail_fn: none_dropdown,
								para: [o,e]
							});
				        } else {
						    none_dropdown(o, e);
						}
					}
				}

				

				var none_dropdown = function(o, e){
				    var html = "<tr id='none_dropdown' style='background-color:aliceblue' title='查无结果'><td align='center'>查无结果</td></tr>";
				    if($("#drop"+$(o).attr("id")+" #none_dropdown").length + $(".click_item"+$(o).attr("id")).length == 0){
				    	 $("#drop"+$(o).attr("id")+" table").append(html);
				    }
				}

				
				var dropdown_menu = function(data_array,o, e){
					$(".click_item"+$(o).attr("id")).off('click mouseover mouseout'); //取消原事件绑定				    
					$(".click_item"+$(o).attr("id")).remove();//移除原有项目
					var data_item = "";
					var show_item = "";
					var item_len = 0;
					var item_acount = 0;
					var html = "";
					if (data_array[0] instanceof Array || data_array[0] instanceof Object){
                        for (var i = 0; i < data_array.length; i++) {
                        	var j = 0;
					        var myvalue = "";
							for(var item in data_array[i]){
								if (j == 0) {
		                        	data_item = data_array[i][item];
							        //if((item_len = strlen_rst(data_item,Math.floor(opts.d_width/9))) > 0){
								        //show_item = data_item.substr(0,item_len-1)+"…";
								    //} else {
								        show_item = data_item;
								    //}	
								} else {
									myvalue += "#/#"+data_array[i][item];
								}
								j++;
							}
							myvalue = myvalue.substr(3);
                            html += "<tr class='click_item"+$(o).attr("id")+"' role='button' style='background-color:aliceblue' title='"+data_item+"' myvalue='"+myvalue+"'><td align='center' style='max-width:"+opts.max_width+";'>"+show_item+"</td></tr>";
				            item_acount++;
						    if (item_acount >= opts.max_len){break;}
                        }
					} else {
                        for (var item in data_array) {
					        //if((item_len = strlen_rst(data_array[item],Math.floor(opts.d_width/9))) > 0){
						        //data_item = data_array[item].substr(0,item_len-1)+"…";
						    //} else {
						        data_item = data_array[item];
						    //}
                            html += "<tr class='click_item"+$(o).attr("id")+"' role='button' style='background-color:aliceblue' title='"+data_array[item]+"' myvalue=''><td align='center' style='max-width:"+opts.max_width+";'>"+data_item+"</td></tr>";
				            item_acount++;
						    if (item_acount >= opts.max_len){break;}
                        }
					}

                    $("#drop"+$(o).attr("id")+" #none_dropdown").remove();
                    $("#drop"+$(o).attr("id")+" table").append(html);
//******************************************************************************
//菜单点击操作
//******************************************************************************
					$(".click_item"+$(o).attr("id")).on('click', function () {
				        $(o).focus();
				        //multiple打开时的菜单点击操作
				        if (opts.multiple != false) {
				        	//获得multiple限制值
				        	var multiple_limit = Number($(o).attr("multiples"));
				        	//小于或等于1不限制
				        	if (multiple_limit <= 1) {
								add_item($(this),o);
				        	} else {
				        		if ($("#base_"+$(o).attr("for")).find("input[type=checkbox]").length < multiple_limit) {
				        			add_item($(this),o);
				        		} else {
				        			alert_flavr("超过数量限制："+multiple_limit);
				        		}
				        	}
				        //force打开时的操作
				        } else if (opts.force == 1) {
				        	add_item($(this),o);
				        } else if (opts.fn != undefined) {
                            opts.fn($(this),o,e); //调用传进来的函数。
                        } else {
                            copy_to_input(o, $(this).attr("title"));
                            //store_filter(o);
						}
						//重置下拉
                        finalize($(o).attr("id"));
                    });

					$("#drop_paste"+$(o).attr("id")).on('mouseover', function (e) {
				        $(this).css("background-color","palegreen");
                    });
					$(".click_item"+$(o).attr("id")).on('mouseover', function (e) {
				        $(this).css("background-color","palegreen");
				        //copy_to_input(o, $(this).attr("title"));
                    });

					$("#drop_paste"+$(o).attr("id")+", .click_item"+$(o).attr("id")).on('mouseout', function (e) {
				        $(this).css("background-color","aliceblue");
                    });
                    //$("#drop"+$(o).attr("id")).on("mouseout",function(e){
                    	//read_filter(o);
                    //});
					/*$("#drop_tip"+).live('click', function () {
				        $(o).focus();
                        finalize($(o).attr("id"));
                        //if (fn != undefined) {
                            //fn(); //调用传进来的函数。
                        //}
                    });*/
				}

				var add_item = function(th,o){
					var m_title = th.attr("title");
					if (th.attr("myvalue").length > 0) {
						var m_value = th.attr("myvalue");
					} else {
						var m_value = m_title;
					}
					if ($(o).attr("multiples") != undefined) {
						add_checkbox(m_value,m_title,o);
		    		} else {
		    			$("#"+$(o).attr("for")).attr("value",m_value);
			        	$("#show_"+$(o).attr("for")).html(m_title);
			        	$(o).val("");
		    		}
		    		//值变化后的回调
		    		var fn = $(o).attr("change_fn");
		    		eval(fn);
				}

				//通过值添加元素
				var add_from_val = function(val,o){
					if ($(o).attr("bind") != undefined && $(o).attr("bind").substr(0,1) == "{") {
						eval("var data_info = "+$(o).attr("bind")+";");
						var model = data_info.model==undefined?"":data_info.model;
						var type = data_info.type==undefined?"":data_info.type;
						var col = data_info.col==undefined?"":data_info.col;
						var show = data_info.show==undefined?"1":data_info.show;
					} else {
		    			var model = $(o).parents(opts.model).attr("model");
			    		var col = $(o).attr("for")==undefined?$(o).attr("name"):$(o).attr("for");
			    		var type = $(o).parents(opts.model).attr("type");
			    		var show = "";
		    		}
					//var model = $(o).parents(opts.model).attr("model");
		    		//var col = $(o).attr("for");
		    		//var type = $(o).attr("bind");

		    		//根据是多选或者单选，不同的添加方式
		    		if ($(o).attr("multiples") != undefined) {
		    			var fn = add_checkbox_array;
		    		} else {
		    			var fn = add_select;
		    		}

					get_bind({
						model: model,
						col: col,
						type: type,
						show: show,
						value: val,
						group: 1,
						fn: fn,
						fail_fn: function(){
							$(o).val("");
						},
						para: [o]
					});
				}

				//增加多选
				var add_checkbox_array = function(data_array,o){
					if (data_array[0] instanceof Array || data_array[0] instanceof Object){
                        for (var i = 0; i < data_array.length; i++) {
                        	var j = 0;
                       		for(var item in data_array[i]){
                       			if (j == 0) {
                       				var title = data_array[i][item];
                       			} else if(j == 1){
                       				var value = data_array[i][item];
                       			}
                       			j++;
                       		}
                       		if (j == 1) {
                       			var value = title;
                       		}
                       		add_checkbox(value,title,o);
                        }
					} else {
                        for (var item in data_array) {
					       	add_checkbox(data_array[item],data_array[item],o);
                        }
					}
				}

				//增加多选的执行
				var add_checkbox = function(m_value,m_title,o){
					if ($("#base_"+$(o).attr("for")).find("input[type=checkbox][value="+m_value+"]").length == 0) {
						$(o).before("<span class=\"flex_no_shrink\" style=\"background-color:#F2DAFA;padding:2px;margin:2px;\"><input type=\"checkbox\" name=\""+String($(o).attr("id")).substr(3)+"\" value=\""+m_value+"\" style=\"display:none;\" checked>"+m_title+"<span style=\"color:#B3B3B3;cursor:pointer;\" class=\"glyphicon glyphicon-remove\" onclick=\"$(this).parent('span').remove();eval($('#base_"+$(o).attr("for")+"').find('[for]').attr('change_fn'));\"></span></span>");
					}
					//清除值
					$("#sp_"+String($(o).attr("id")).substr(3)).val("");
					//值变化后的回调
		    		var fn = $(o).attr("change_fn");
		    		eval(fn);
				}

				var add_select = function(data_array,o){
					if (data_array[0] instanceof Array || data_array[0] instanceof Object){
						var i = 0;
						for(var item in data_array[0]){
							if (i == 0) {
								var title = data_array[0][item];
								$("#show_"+$(o).attr("for")).html(title);
							}
							if (i == 1) {
								$("#"+$(o).attr("for")).val(data_array[0][item]);
							}
							i++;
						}
						if (i == 1) {
							$("#"+$(o).attr("for")).val(title);
						}
					} else {
						var i = 0;
						for(var item in data_array){
							if (i == 0) {
								var title = data_array[item];
								$("#show_"+$(o).attr("for")).html(title);
							}
							if (i == 1) {
								$("#"+$(o).attr("for")).val(data_array[item]);
							}
							i++;
						}
						if (i == 1) {
							$("#"+$(o).attr("for")).val(title);
						}
					}
					
					$(o).val("");

					//值变化后的回调
		    		var fn = $(o).attr("change_fn");
		    		eval(fn);
				}


				

				var finalize = function(tmpid){
				    $(".click_item" + tmpid).off('click mouseover mouseout'); //取消事件绑定
				    $("#drop_paste" + tmpid).off('click mouseover mouseout'); //取消事件绑定
                    $("#drop" + tmpid).off('mouseout');
                    $("#drop" + tmpid).remove();
				}
				/*
				var forbid_value = function(id){
					if ($("#"+id).attr("bind").substr(0,1) == "[") {
			    		eval("var data_bind = "+$("#"+id).attr("bind")+";");
			    		if (Number($.inArray($("#"+id).val(),data_bind)) == -1) {
			    			$("#"+id).val("");
			    		}	
			    	} else {
						var model = $("#"+id).parents(".ajax_input").attr("model");
			    		var col = $("#"+id).attr("name");
			    		var type = $("#"+id).attr("bind");
				    	
					    
					    
						//is_bind({
							//model: model,
							//col: col,
							//type: type,
							//value: $("#"+id).val(),
							//fail_fn: function(){
								//$("#"+id).val("");
							//},
						//});
					}
				}
				*/


				return this.each(function(){
					

					if (typeof($(this).attr("id") == "undefined")) {
						$(this).attr("id",$(this).attr("name"));
					}
					
					if (opts.multiple == true || opts.multiple == 1) {
						$(this).wrap("<div id=\"base_"+$(this).attr("id")+"\" type=\"divtext\" class=\""+$(this).attr("class")+" inline_flex\" style=\"position:relative\" onclick=\"$(this).children('input[type=text]').focus();\"></div>");
						$(this).after("<span class=\"caret\" style=\"position:absolute;top:46%;right:10px;\"></span>");
						
						//$(this).before("<font size=\"1\" style=\"position:absolute;top:-2px;left:0px;width:50px;max-width:50px;text-align:left;overflow:hidden;white-space:nowrap;color:lightgrey;background-color:#EFF7FD;opacity:0.5;\">筛选:<span id=\"filter_"+$(this).attr("id")+"\"></span></font>");
						//$(this).after("<input type=\"hidden\" id=\""+$(this).attr("id")+"\" name=\""+$(this).attr("name")+"\" value=\"\">");
						//$(this).after("<>dd</div>");
						if ($(this).attr("nullable") != undefined) {
							$("#base_"+$(this).attr("id")).attr("nullable",$(this).attr("nullable"));
						}
						$(this).attr("sp","1");
						$(this).attr("for",$(this).attr("name"));
						$(this).attr("id","sp_"+$(this).attr("id"));
						$(this).attr("name","sp_"+$(this).attr("name"));
						$(this).attr("data","0");
						$(this).attr("class","transparent-input");
						if($(this).val().length > 0){
							add_from_val($(this).val(),$(this));
						}
						//$(this).css("width","auto");

					} else if (opts.force == 1){
						$(this).wrap("<div id=\"base_"+$(this).attr("id")+"\" type=\"divtext\" class=\""+$(this).attr("class")+" inline_flex\" style=\"position:relative\" onclick=\"$(this).children('input[type=text]').focus();\"></div>");
						$(this).after("<span class=\"caret\" style=\"position:absolute;top:46%;right:10px;\"></span>");
						if ($(this).attr("nullable") != undefined) {
							$("#base_"+$(this).attr("id")).attr("nullable",$(this).attr("nullable"));
						}
						$(this).attr("sp","1");
						$(this).parent("div").append("<input type=\"hidden\" class=\"real_data\" id=\""+$(this).attr("name")+"\" name=\""+$(this).attr("name")+"\" value=\"\">");
						$(this).parent("div").prepend("<span id=\"show_"+$(this).attr("name")+"\" class=\"real_show flex_no_shrink\"></span>");
						$(this).attr("for",$(this).attr("name"));
						$(this).attr("id","sp_"+$(this).attr("id"));
						$(this).attr("name","sp_"+$(this).attr("name"));
						$(this).attr("data","0");
						$(this).attr("class","transparent-input");
						$(this).css("width","100%");
						$(this).css("float","left");
						if($(this).val().length > 0){
							add_from_val($(this).val(),$(this));
						}
					} else {

						$(this).wrap("<div style=\"position:relative\"></div>");
						if ($(this).attr("tips") != undefined) {
							$(this).after($(this).attr("tips"));
						} else {
							$(this).after("<span class=\"glyphicon glyphicon-pencil\" style=\"position:absolute;top:30%;right:5px;\"></span>");
						}
						
					}

				    $(this).on("contextmenu", function(e){return false;});
				    $(this).blur(function(e){
				    	if (!$(this).hasClass("disabled")) {
					        if (opts.null_tip == 1){
							    null_tip($(this));							
							}
							setTimeout('$("#drop'+$(this).attr("id")+'").remove()', 300);
							if(opts.force == 1){
								setTimeout('$("#'+$(this).attr("id")+'").val("")', 500);
							}
						}
				    });
			        //$(this).focus(function(){
				    //});
			        $(this).on("keyup focus",function(e){
				    	if (!$(this).hasClass("disabled")) {
					        $(this).css("background-color","");
					        dropdown_menu_show($(this), e);
				        } else {
				        	$(this).blur();
				        }
				    });

				    $(this).on("dblclick",function(){
				    	if ($(this).attr("for") != undefined && $(this).attr("refresh") == "1") {
				    		if ($(this).val().length > 0) {
				    			$(this).attr("refresh",0);
				    			add_from_val($(this).val(),$(this));
				    		}
				    	}
				    });
					/* 
					if (opts.drop_type == "mouse"){
					    $(this).mousedown(function(e){
					        if (e.which == 3){
						        dropdown_menu_show($(this), e);
						    }
					    });
					}else {
						$(this).click(function(e){
						    if($("#drop"+$(this).attr("id")).length == 0){
						        dropdown_menu_show($(this), e);
							}
					    });
					}*/
				});

			}
		})(jQuery);