<?php
//判断SQL表达式
function sql_item_fn($str){
	$base = array("COUNT","CONCAT","TRUNCATE","GROUP_CONCAT","SUM","DISTINCT","IF","MAX","MIN");
	foreach ($base as $item) {
		if (strpos($str,$item) === 0) {
			return $item;
		}
	}
	return false;
}



//array转序列
function array_to_string($array_input, $aside_str = "", $mid_str = ","){
	if (is_array($array_input)) {
		$return_text = "";
		for($i = 0; $i < sizeof($array_input); $i++){
			$return_text .= $mid_str.$aside_str.$array_input[$i].$aside_str;
		}
		$return_text = substr($return_text, 1);
		return $return_text;
	} else {
		return $array_input;
	}
}
//序列转array
function string_to_array($text_input, $aside_str = "", $mid_str = ","){
	$aside_length = strlen($aside_str);
	$text_change = substr($text_input,$aside_length,(strlen($text_input)-2*$aside_length));
	$return_array = explode($aside_str.$mid_str.$aside_str,$text_change);
	return $return_array;
}
//多选字符串“{}”与数组互转
function multiple_to_array($text_input){
	if (substr($text_input,0,1) == "{" && substr($text_input,-1) == "}") {
		return explode("}{",substr($text_input,1,strlen($text_input)-2));
	} else {
		return array($text_input);
	}
}
function array_to_multiple($array_input){
	$r_text = "";
	foreach ($array_input as $value) {
		$r_text .= "}{".$value;
	}
	return substr($r_text,1)."}";
}
//判断字符串是否含有中文，0——没有，1——含有，2——全是
function chinesechar($text){
    if (preg_match("/[\x7f-\xff]/",$text)){
	    //if (!preg_match("/[\x80-\xff]/",$text)){
		    //return 2;
		//} else {
		    return 1;
		//}
	} else {
	    return 0;
	}
}




function view_info($html,$target,$msg,$tag="",$type="php"){
	$rep_item = "";
	$tag_before = "";
	$tag_after = "";
	if ($tag == "") {
		$tag_info = 0;
	} else if (strpos($tag,"#/#0#/#")) {
		$tag_info = 2;
	} else {
		$tag_info = 1;
	}
	//echo strpos("#/#0#/#",$tag);
	if ($tag_info == 1) {
		$tag_sep = explode(" ",$tag);
		if (sizeof($tag_sep) > 1) {
			$tag_before = "<".$tag_sep[0]." ".$tag_sep[1].">";
		} else {
			$tag_before = "<".$tag_sep[0].">";
		}
		$tag_after = "</".$tag_sep[0].">";
	}
	if (is_array($msg)){
		if (isset($msg[0]) && is_array($msg[0])) {
			for ($i=0; $i < sizeof($msg); $i++) {
				if ($tag_info == 1) {
					$tag_before_tmp = $tag_before;
					for ($j=1; $j < sizeof($tag_sep); $j++) { 
						$tag_before_tmp = str_replace("#/#".$j."#/#",$msg[$i][$j],$tag_before_tmp);
					}
					$rep_item .= $tag_before_tmp.$msg[$i][0].$tag_after;
				} else if($tag_info == 2){
					$tag_model = $tag;
					for ($j=0; $j < sizeof($msg[0]); $j++) { 
						$tag_model = str_replace("#/#".$j."#/#",$msg[$i][$j],$tag_model);
					}
					$rep_item .= $tag_model;
				} else {
					$rep_item .= $msg[$i][0];
				}
			}
			//$rep_item .= $tag_before.$msg[$i].$tag_after;
		} else {
			for ($i=0; $i < sizeof($msg); $i++) { 
				$rep_item .= $tag_before.$msg[$i].$tag_after;
			}
		}
	} else {
		$rep_item = $tag_before.$msg.$tag_after;
	}
	switch ($type) {
	 	case "php":
	 		$rep_before = "<!--";
	 		$rep_after = "-->";
	 		break;

	 	case "js":
	 		$rep_before = "//";
	 		$rep_after = "//";
	 		break;
	 	
	 	default:
	 		$rep_before = "<!--";
	 		$rep_after = "-->";
	 		break;
	}
	$html = str_replace($rep_before.$target.$rep_after, $rep_item, $html);
	return $html;
}


//焊缝级别判断函数	
	
function level_cal($medium, $pressure, $temperature, $ac, $bc){
	$a_grade = base_grade_cal($ac);
	$b_grade = base_grade_cal($bc);
	if ($a_grade === false || $b_grade === false || ($a_grade != "AⅠ" && $a_grade != "CⅠ" && $a_grade != "CⅡ" && $a_grade != "CⅢ") || ($b_grade != "AⅠ" && $b_grade != "CⅠ" && $b_grade != "CⅡ" && $b_grade != "CⅢ")){
		return "一级";
	} else if ($medium == "油" || $medium == "氢气" || $medium == "氢气" || $medium == ""){
		return "一级";
	} else if (floatval($pressure) >= 4 || $pressure == "" || floatval($temperature) >= 200 || $temperature == ""){
		return "一级";
	} else if (floatval($pressure) < 1.6){
		return "二级";
	} else {
		return "二级";
	}
}
	
//焊缝级别判断函数
function base_grade_cal($c){
	$setting = new \App\setting();
	$r = $setting->where("setting_type","basemetal")->where("setting_name",$c)->get();
	if ($r->isEmpty()) {
		return false;
	} else {
		return $r[0]->setting_r0;
	}
}
	
//焊缝检验比例计算函数
function exam_rate_confirm($rate_text, $level, $pressure_test, $ac, $at, $ath, $bc, $bt, $bth, $jtype){
	$rate_array = explode(",",$rate_text);
	$cal_rate = exam_rate_cal($level, $pressure_test, $ac, $at, $ath, $bc, $bt, $bth, $jtype);
	for ($i = 0; $i < sizeof($rate_array); $i++){
		$rate_array[$i] = intval($rate_array[$i]); 
		$cal_rate[$i] = intval($cal_rate[$i]); 
	}
	if ((MAX($rate_array[0],$cal_rate[1]) >= MAX($cal_rate[0],$cal_rate[1])) && (MAX($rate_array[2],$rate_array[3]) >= MAX($cal_rate[2],$cal_rate[3]))){
		return 1;
	} else {
		return 0;
	}
}
function exam_rate_cal($level, $pressure_test, $ac, $at, $ath, $bc, $bt, $bth, $jtype="对接"){
	$a_grade = base_grade_cal($ac);
	$b_grade = base_grade_cal($bc);
	if ($jtype == ""){$jtype = "对接";}
	if ($pressure_test == ""){$pressure_test = 0;}
	$exam_rate = array();
	$v_exam = 0;
	$s_exam = 0;
	$gp_exam = 0;
	$hb_exam = 0;
	if ($level == "一级"){
		if ($jtype == "对接"){
			if (intval($pressure_test) == 0 || floatval(Max($at, $bt)) > 168 || floatval(Max($ath, $bth)) > 25){
				$v_exam = 100;
				$s_exam = 100;
			} else {
				$v_exam = 10;
				$s_exam = 10;
			}
		} else {
			if ($at > $bt){
				$min_t = $bt;
				$min_th = $bth;
			} else {
				$min_t = $at;
				$min_th = $ath;
			}
			if (intval($pressure_test) == 0 || floatval($min_t) > 168 || floatval($min_th) > 25){
				$v_exam = 0;
				$s_exam = 100;
			} else {
				$v_exam = 0;
				$s_exam = 10;
			}
		}
	} else {
		if(intval($pressure_test) == 0){
			if ($jtype == "对接"){
				if (floatval(Max($at, $bt)) > 168){
					$v_exam = 100;
					$s_exam = 0;
				} else {
					$v_exam = 10;
					$s_exam = 0;
				}
			} else if ($jtype == "支管") {
				$v_exam = 0;
				$s_exam = 100;
			} else {
				$v_exam = 0;
				$s_exam = 10;
			}
		} else {
			$v_exam = 0;
			$s_exam = 0;
		}
	}
	//光谱计算
	if (($a_grade != "AⅠ" && $a_grade != "CⅠ" && $a_grade != "CⅡ" && $a_grade != "CⅢ") || ($b_grade != "AⅠ" && $b_grade != "CⅠ" && $b_grade != "CⅡ" && $b_grade != "CⅢ")){
		if (floatval(Min($at, $bt)) > 168 || ($jtype == "对接" && floatval(Max($at, $bt)) > 168)){
			$gp_exam = 100;
		} else {
			$gp_exam = 20;
		}
	}
	//硬度计算
	if (floatval(Min($at, $bt)) > 168 || ($jtype == "对接" && floatval(Max($at, $bt)) > 168)){
		$hb_exam = 100;
	}
	$exam_rate[] = $v_exam;
	$exam_rate[] = 0;
	if (floatval(Max($at, $bt)) > 114.3 && $a_grade != "CⅠ" && $a_grade != "CⅡ" && $a_grade != "CⅢ" && $b_grade != "CⅠ" && $b_grade != "CⅡ" && $b_grade != "CⅢ"){
		$exam_rate[] = 0;
		$exam_rate[] = $s_exam;
	} else {
		$exam_rate[] = $s_exam;
		$exam_rate[] = 0;
	}
	$exam_rate[] = $gp_exam;
	$exam_rate[] = 0;
	return $exam_rate;
}
