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
	$return_text = "";
	for($i = 0; $i < sizeof($array_input); $i++){
		$return_text .= $mid_str.$aside_str.$array_input[$i].$aside_str;
	}
	$return_text = substr($return_text, 1);
	return $return_text;
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