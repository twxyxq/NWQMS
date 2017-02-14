		</div>
	</div>
	<script type="text/javascript">
		$(".ajax_input input").click(function(){
			$(this).removeClass("form_null");
		});
		$(".ajax_submit").click(function(){
			var btn_obj = $(this);
			var form_obj = btn_obj.parents(".ajax_input");
			if (form_obj.attr("nullable") == "set") {
				var find_text = "input[type=text][nullable=1]";
			} else if (form_obj.attr("nullable") == "except") {
				var find_text = "input[type=text][nullable!=0]";
			} else {
				var find_text = "input[type=text]";
			}
			var null_num = 0;
			form_obj.find(find_text).each(function(){
				if ($(this).val().length == 0) {
					$(this).addClass("form_null");
					null_num++;
				}
			});
			if (null_num == 0) {
				var postdata = {};
				postdata["model"] = form_obj.attr("model");
				if (form_obj.attr("data") == "set") {
					var find_text = "[data=1]";
				} else if (form_obj.attr("data") == "except") {
					var find_text = "[data!=0]";
				} else {
					var find_text = "input[name],select[name],radio[checked]";
				}
				form_obj.find(find_text).each(function(){
					//alert($(this).attr("id")+$(this).attr("value"));
					postdata[$(this).attr("name")] = $(this).val();
					//$(this).removeAttr("style");
				});
				postdata["insert"] = 1;
				$.post("/console/model_ajax", postdata, function(data){
					if ($.trim(data).substr(0,1) != "{" || $.trim(data).substr($.trim(data).length-1,1) != "}"){
						alert("操作失败！错误信息："+data);
					}
					eval('var rdata = '+data);
					//alert(rdata.suc); 
					if (Number(rdata.suc) == 1) {
						alert(rdata.msg);
						$('#example').DataTable().draw();
						if (form_obj.attr("clear") == "set") {
							form_obj.find("[clear=1]").val("");
						} else {
							form_obj.find("input[type=text]").val("");//clear="all" or not set clear
						}
						
					} else {		
						alert(rdata.msg);
					}
				});	
			}
		});

		//$("input[bind]").click(function(){
			//get_model_data({
				//fn: dosomething,
				//para: [1,2]
			//});
		//});


		
		$("input[bind]").intelligent_input({
			force: 1 
		});
		//$("input[refer]").intelligent_input({
			//force: 0 
		//});

		//console.log($.fn);
	</script>
</body>
</html>