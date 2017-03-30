<?php

/**
* 
*/
class nav
{
	public $module = array();
	public $current_controller;
	public $current_method;
	public $current_item = false;
	public $current_module = false;
	
	function __construct($controller,$method)
	{


		$this->add_module("panel/wj","焊口");
		$this->add_module("panel/weld","焊接");
		$this->add_module("panel/pp","人员");
		$this->add_module("panel/material","材料");
		$this->add_module("panel/exam","检验");
		$this->add_module("panel/setting","设置");


		$this->module["panel/wj"]->child(nav_item::create("panel/wj_in","焊口录入",array(
				nav_item::create("wj/excel_input","清单导入"),
				nav_item::create("wj/manual_input","手动录入"),
				nav_item::create("wj/wj_single_add","单个录入")
			)));

		$this->module["panel/weld"]->child(nav_item::create("panel/tsk","施工任务",array(
				nav_item::create("tsk/tsk_add","任务添加"),
				nav_item::create("tsk/tsk_list","任务清单"),
				nav_item::create("tsk/tsk_finish","完工录入")
			)));
		$this->module["panel/weld"]->child(nav_item::create("panel/consignation","检验委托",array(
				nav_item::create("consignation/manual_add?emethod=RT","RT委托"),
				nav_item::create("consignation/manual_add?emethod=UT","UT委托"),
				nav_item::create("consignation/manual_add?emethod=PT","PT委托"),
				nav_item::create("consignation/manual_add?emethod=MT","MT委托"),
				nav_item::create("consignation/manual_add?emethod=SA","光谱委托"),
				nav_item::create("consignation/manual_add?emethod=HB","硬度委托"),
				nav_item::create("consignation/group_list","分组清单"),
				nav_item::create("consignation/no_sheet","打印委托"),
				nav_item::create("consignation/consignation_sheet","委托单")
			)));
		$this->module["panel/weld"]->child(nav_item::create("panel/qp","质量计划",array(
				nav_item::create("qp/qp_add","添加质量计划"),
				nav_item::create("qp/qp_list","质量计划清单"),
				nav_item::create("qp/qp_proc","质量计划工序")
			)));
		$this->module["panel/weld"]->child(nav_item::create("panel/wpq","工艺评定",array(
				nav_item::create("wpq/wpq_add","添加工艺评定"),
				nav_item::create("wpq/wpq_proc","工艺评定审核"),
				nav_item::create("wpq/wpq_list","工艺评定清单")
			)));
		$this->module["panel/weld"]->child(nav_item::create("panel/wps","工艺卡",array(
				nav_item::create("wps/wps_add","添加工艺卡"),
				nav_item::create("wps/wps_proc","工艺卡审核"),
				nav_item::create("wps/wps_list","工艺卡清单")
			)));

		$this->module["panel/pp"]->child(nav_item::create("panel/pp_base","基础信息",array(
				nav_item::create("pp/pp_add","人员录入"),
				nav_item::create("pp/pp_in_out","进出场")
			)));

		$this->module["panel/pp"]->child(nav_item::create("panel/pp_qf","资质信息",array(
				nav_item::create("pp/pp_scan","核级证书录入"),
				nav_item::create("pp/qf_valided","已验证证书")
			)));
		$this->module["panel/pp"]->child(nav_item::create("panel/qf_valid","抽项考试",array(
				nav_item::create("pp/qf_validation_plan","考试计划"),
				nav_item::create("pp/qf_validation_result","结果确认"),
				nav_item::create("pp/qf_validation_list","抽项考试清单")
			)));

		$this->module["panel/material"]->child(nav_item::create("panel/material_sheet","焊材领用单",array(
				nav_item::create("material/sheet_add","生成领用单"),
				nav_item::create("material/sheet_list","领用单列表")
			)));
		$this->module["panel/material"]->child(nav_item::create("panel/warehouse?store=LOC","现场焊材库",array(
				nav_item::create("material/in?store=LOC","入库"),
				nav_item::create("material/out?store=LOC","出库"),
				nav_item::create("material/store_list?store=LOC","库存"),
				nav_item::create("material/store_record?store=LOC","进出记录"),
				nav_item::create("material/sent?store=LOC","焊材领用"),
				nav_item::create("material/back?store=LOC","焊材回收")
			)));
		$this->module["panel/material"]->child(nav_item::create("panel/warehouse?store=PRE","准备区焊材库",array(
				nav_item::create("material/in?store=PRE","入库"),
				nav_item::create("material/out?store=PRE","出库"),
				nav_item::create("material/store_list?store=PRE","库存"),
				nav_item::create("material/store_record?store=PRE","进出记录"),
				nav_item::create("material/sent?store=PRE","焊材领用"),
				nav_item::create("material/back?store=PRE","焊材回收")
			)));



		$this->module["panel/setting"]->child(nav_item::create("panel/common_setting","常规设置",array(
				nav_item::create("setting/basetype","母材类型"),
				nav_item::create("setting/basemetal","母材材质"),
				nav_item::create("setting/medium","管道介质"),
				nav_item::create("setting/jtype","接头型式"),
				nav_item::create("setting/gtype","坡口型式"),
				nav_item::create("setting/wmethod","焊接方法")
			)));

		$this->module["panel/setting"]->child(nav_item::create("panel/material_setting","焊材设置",array(
				nav_item::create("setting/supplier","供应商"),
				nav_item::create("setting/wmtype","焊材型号"),
				nav_item::create("setting/wmtrademark","焊材牌号")
			)));

		$this->module["panel/setting"]->child(nav_item::create("panel/exam_setting","检验设置",array(
				nav_item::create("setting/examrate","检验比例")
			)));

		//$this->module["setting"]->child(nav_item::create("setting/supplier","供应商","setting@supplier"));

		//$this->current_from_url();


		$this->current_controller = $controller;
		$this->current_method = $method;
		$this->current_item = $this->current_item();
		$this->current_module = $this->current_module();
		//dd($this->current_item());
	}


	function add_module($tag,$title,$controller=""){
		$this->module[$tag] = new nav_item($tag,$title,$controller);
	}

	function current_item(){
		$add_on = $_SERVER["QUERY_STRING"];
		if (strlen($add_on) > 0) {
			$add_on = "?".$add_on;
		}
		$return = false;
		foreach ($this->module as $value) {
			$this->item_find($value,function($item) use ($add_on){
				if ($item->controller == $this->current_controller."@".$this->current_method.$add_on) {
					return true;
				}
			},$return);
		}
		return $return;
	}

	function item_find($item,$fn,&$return){
		//print_r($item);
		if ($fn($item)) {
			$return = $item;
		}
		foreach ($item->item as $value) {
			$this->item_find($value,$fn,$return);
		}
	}

	function current_module(){
		if ($this->current_item == false) {
			return false;
		} else {
			return $this->current_item->parents()[0];
		}
	}

	function module_data($except=false){
		$data = array();
		foreach ($this->module as $value) {
			if ($except === false || ($value->tag != $except && $value->title != $except)) {
				$data[] = array($value->tag,$value->title);
			}		
		}
		return $data;
	}

	function secondary_data($current_txt=false){
		$data = array();
		if (sizeof($this->current_item->parents()) > 1) {
			foreach ($this->current_module->item as $item) {
				$current_idt = "";
				if ($current_txt !== false && $item->tag == $this->current_item->parents()[1]->tag) {
					$current_idt = $current_txt;
				}
				$data[] = array($item->tag,$item->title,$current_idt);
			}	
		}
		return $data;
	}

	function nav_data(){
		$data = $this->current_module->childrens_array();
	}
}


/**
* 
*/
class nav_item
{
	public $title;
	public $tag;
	public $item = array();
	public $parent = false;
	public $controller;
	
	function __construct($tag,$title,$controller="")
	{
		$this->tag = $tag;
		$this->title = $title;
		if ($controller == "") {
			$this->controller = str_replace("/","@",$tag);
		} else if (substr($this->controller,-1) == "@") {
			$this->controller = $controller.$tag;
		} else {
			$this->controller = $controller;
		}
	}

	public static function create($tag,$title,$controller="",$children_array=array()){
		if (is_array($controller) && sizeof($children_array) == 0) {
			$children_array = $controller;
			$controller = "";
		}
		$item = new nav_item($tag,$title,$controller);
		for ($i=0; $i < sizeof($children_array); $i++) { 
			$item->child($children_array[$i]);
		}
		return $item;
	}

	function child(nav_item $child){
		$child->parent = $this;
		$this->item[] = $child;
	}

	function children_array(){
		$r_array = array();
		for ($i=0; $i < sizeof($this->item); $i++) { 
			$r_array[] = array($this->item[$i]->tag,$this->item[$i]->title);
		}
		return $r_array;
	}

	function parents(){
		$parents = array($this);
		$item = $this;
		while ($item->parent !== false) {
			$item = $item->parent;
			array_unshift($parents,$item);
		}
		return $parents;
	}
}