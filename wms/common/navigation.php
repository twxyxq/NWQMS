<?php


use Illuminate\Support\Facades\Auth;
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
		if (env("APP_SYSTEM","CI") == "CI") {
			

			$this->add_module(nav_item::create("panel/wj","焊口","glyphicon glyphicon-info-sign"));
			$this->add_module(nav_item::create("panel/weld","焊接","glyphicon glyphicon-baby-formula"));
			$this->add_module(nav_item::create("panel/pp","人员","glyphicon glyphicon-user"));
			$this->add_module(nav_item::create("panel/material","材料","glyphicon glyphicon-oil"));
			$this->add_module(nav_item::create("panel/exam","检验","glyphicon glyphicon-search"));
			$this->add_module(nav_item::create("panel/setting","设置","glyphicon glyphicon-cog"));
			$this->add_module(nav_item::create("panel/alternation","变更","glyphicon glyphicon-list-alt"));
			$this->add_module(nav_item::create("panel/statistic","统计","glyphicon glyphicon-stats"));


			$this->module["panel/wj"]->child(nav_item::create("panel/wj_l","焊口清单",array(
					nav_item::create("wj/wj_list","焊口清单","glyphicon glyphicon-list"),
					nav_item::create("wj/wj_exec_list","焊口执行情况","glyphicon glyphicon-th-list")
				),"glyphicon glyphicon-list"));
			$this->module["panel/wj"]->child(nav_item::create("panel/wj_import","焊口导入",array(
					nav_item::create("wj/excel_input","excel导入","glyphicon glyphicon-import"),
					nav_item::create("wj/import_check","导入确认","glyphicon glyphicon-save-file")
				),"glyphicon glyphicon-import"));
			$this->module["panel/wj"]->child(nav_item::create("panel/wj_input","手工录入",array(
					nav_item::create("wj/manual_input","表格录入","glyphicon glyphicon-th-large"),
					nav_item::create("wj/wj_single_add","单个录入","glyphicon glyphicon-pencil"),
					nav_item::create("wj/wj_single_structure_add","结构焊缝录入","glyphicon glyphicon-magnet"),
					nav_item::create("wj/manual_check","手动录入确认","glyphicon glyphicon-save-file")
				),"glyphicon glyphicon-pencil"));
			$this->module["panel/wj"]->child(nav_item::create("panel/wj_check","焊口审核",array(
					nav_item::create("wj/check_list","待审核","glyphicon glyphicon-hourglass"),
					nav_item::create("wj/checked_list","审核完成","glyphicon glyphicon-ice-lolly")
				),"glyphicon glyphicon-sunglasses"));
			$this->module["panel/wj"]->child(nav_item::create("panel/wj_rr","焊口返修",array(
					nav_item::create("wj/wj_r","焊口返修","glyphicon glyphicon-repeat"),
					nav_item::create("wj/wj_r_list","返修清单")
				),"glyphicon glyphicon-repeat"));

			$this->module["panel/weld"]->child(nav_item::create("panel/tsk","施工任务",array(
					nav_item::create("tsk/tsk_add","任务添加","glyphicon glyphicon-log-in"),
					nav_item::create("tsk/tsk_list","任务清单","glyphicon glyphicon-tasks"),
					nav_item::create("tsk/tsk_unfinished_list","未完任务","glyphicon glyphicon-th-list"),
					nav_item::create("tsk/tsk_my_list","我的任务","glyphicon glyphicon-briefcase"),
					nav_item::create("tsk/tsk_finish","完工录入","glyphicon glyphicon-saved")
				),"glyphicon glyphicon-tasks"));
			$this->module["panel/weld"]->child(nav_item::create("panel/consignation","检验委托",array(
					nav_item::create("consignation/manual_add?emethod=RT","RT委托","glyphicon glyphicon-hdd"),
					nav_item::create("consignation/manual_add?emethod=UT","UT委托","glyphicon glyphicon-hdd"),
					nav_item::create("consignation/manual_add?emethod=PT","PT委托","glyphicon glyphicon-hdd"),
					nav_item::create("consignation/manual_add?emethod=MT","MT委托","glyphicon glyphicon-hdd"),
					nav_item::create("consignation/manual_add?emethod=SA","光谱委托","glyphicon glyphicon-hdd"),
					nav_item::create("consignation/manual_add?emethod=HB","硬度委托","glyphicon glyphicon-hdd"),
					nav_item::create("consignation/manual_addition_add?emethod=RT","RT额外委托","glyphicon glyphicon-folder-open"),
					nav_item::create("consignation/manual_addition_add?emethod=UT","UT额外委托","glyphicon glyphicon-folder-open"),
					nav_item::create("consignation/manual_addition_add?emethod=PT","PT额外委托","glyphicon glyphicon-folder-open"),
					nav_item::create("consignation/manual_addition_add?emethod=MT","MT额外委托","glyphicon glyphicon-folder-open"),
					nav_item::create("consignation/manual_addition_add?emethod=SA","光谱额外委托","glyphicon glyphicon-folder-open"),
					nav_item::create("consignation/manual_addition_add?emethod=HB","硬度额外委托","glyphicon glyphicon-folder-open"),
					nav_item::create("consignation/group_list","分组清单","glyphicon glyphicon-list"),
					nav_item::create("consignation/consignation_addition","分组检验情况","glyphicon glyphicon-list-alt"),
					nav_item::create("consignation/consignation_addition?unaccpet=1","复验","glyphicon glyphicon-repeat")
				),"glyphicon glyphicon-hdd"));
			$this->module["panel/weld"]->child(nav_item::create("panel/consignation_sheet","委托单",array(
					nav_item::create("consignation/no_sheet?emethod=RT","打印RT委托","glyphicon glyphicon-print"),
					nav_item::create("consignation/no_sheet?emethod=UT","打印UT委托","glyphicon glyphicon-print"),
					nav_item::create("consignation/no_sheet?emethod=PT","打印PT委托","glyphicon glyphicon-print"),
					nav_item::create("consignation/no_sheet?emethod=MT","打印MT委托","glyphicon glyphicon-print"),
					nav_item::create("consignation/no_sheet?emethod=SA","打印光谱委托","glyphicon glyphicon-print"),
					nav_item::create("consignation/no_sheet?emethod=HB","打印硬度委托","glyphicon glyphicon-print"),
					nav_item::create("consignation/consignation_sheet?emethod=RT","RT委托单","glyphicon glyphicon-book"),
					nav_item::create("consignation/consignation_sheet?emethod=UT","UT委托单","glyphicon glyphicon-book"),
					nav_item::create("consignation/consignation_sheet?emethod=PT","PT委托单","glyphicon glyphicon-book"),
					nav_item::create("consignation/consignation_sheet?emethod=MT","MT委托单","glyphicon glyphicon-book"),
					nav_item::create("consignation/consignation_sheet?emethod=SA","光谱委托单","glyphicon glyphicon-book"),
					nav_item::create("consignation/consignation_sheet?emethod=HB","硬度委托单","glyphicon glyphicon-book")
				),"glyphicon glyphicon-book"));
			$this->module["panel/weld"]->child(nav_item::create("panel/qp","质量计划",array(
					nav_item::create("qp/qp_add","添加质量计划","glyphicon glyphicon-shopping-cart"),
					nav_item::create("qp/qp_list","质量计划清单","glyphicon glyphicon-list"),
					nav_item::create("qp/qp_proc","质量计划工序","glyphicon glyphicon-sort-by-attributes")
				),"glyphicon glyphicon-modal-window"));
			$this->module["panel/weld"]->child(nav_item::create("panel/wpq","工艺评定",array(
					nav_item::create("wpq/wpq_add","添加工艺评定","glyphicon glyphicon-play-circle"),
					nav_item::create("wpq/wpq_proc","工艺评定审核","glyphicon glyphicon-eye-open"),
					nav_item::create("wpq/wpq_list","工艺评定清单","glyphicon glyphicon-list")
				),"glyphicon glyphicon-king"));
			$this->module["panel/weld"]->child(nav_item::create("panel/wps","工艺卡",array(
					nav_item::create("wps/wps_add","添加工艺卡","glyphicon glyphicon-play-circle"),
					nav_item::create("wps/wps_proc","工艺卡审核","glyphicon glyphicon-eye-open"),
					nav_item::create("wps/wps_list","工艺卡清单","glyphicon glyphicon-list")
				),"glyphicon glyphicon-knight"));

			$this->module["panel/pp"]->child(nav_item::create("panel/pp_base","基础信息",array(
					nav_item::create("pp/pp_add","人员录入","glyphicon glyphicon-user"),
					nav_item::create("pp/pp_in_out","进出场","glyphicon glyphicon-retweet"),
					nav_item::create("pp/pp_qrcode","二维码打印","glyphicon glyphicon-qrcode")
				),"glyphicon glyphicon-user"));

			$this->module["panel/pp"]->child(nav_item::create("panel/pp_qf","资质信息",array(
					nav_item::create("pp/pp_scan","核级证书录入"),
					nav_item::create("pp/qf_valided","已验证证书"),
					nav_item::create("pp/qf_range","资质匹配性"),
					nav_item::create("pp/qf_range_list","资质匹配清单")
				),array(1)));
			$this->module["panel/pp"]->child(nav_item::create("panel/pp_wj","焊缝梳理",array(
					nav_item::create("ccp/node","节点维护"),
					nav_item::create("ccp/ccp_wj","焊缝梳理")
				),array(1)));
			$this->module["panel/pp"]->child(nav_item::create("panel/qf_valid","抽项考试",array(
					nav_item::create("pp/qf_validation_plan","考试计划"),
					nav_item::create("pp/qf_validation_result","结果确认"),
					nav_item::create("pp/qf_validation_list","抽项考试清单")
				),array(1)));

			$this->module["panel/material"]->child(nav_item::create("panel/material_sheet","焊材领用单",array(
					nav_item::create("material/sheet_add","生成领用单","glyphicon glyphicon-folder-close"),
					nav_item::create("material/sheet_list","领用单列表","glyphicon glyphicon-list"),
					nav_item::create("material/sheet_list_spot","点口单列表","glyphicon glyphicon-list")
				),"glyphicon glyphicon-folder-close"));
			$this->module["panel/material"]->child(nav_item::create("panel/warehouse?warehouse=LOC","现场焊材库",array(
					nav_item::create("material/in?warehouse=LOC","入库","glyphicon glyphicon-log-in"),
					nav_item::create("material/out?warehouse=LOC","出库","glyphicon glyphicon-log-out"),
					nav_item::create("material/store_list?warehouse=LOC","库存","glyphicon glyphicon-home"),
					nav_item::create("material/store_record?warehouse=LOC","进出记录","glyphicon glyphicon-list"),
					nav_item::create("material/sent?warehouse=LOC","焊材发放","glyphicon glyphicon-share"),
					nav_item::create("material/back?warehouse=LOC","焊材回收","glyphicon glyphicon-share-alt"),
					nav_item::create("material/sheet_list?warehouse=LOC","领用单记录","glyphicon glyphicon-list"),
					nav_item::create("material/sheet_list_spot?warehouse=LOC","点口单记录","glyphicon glyphicon-list")
				),"glyphicon glyphicon-home"));
			$this->module["panel/material"]->child(nav_item::create("panel/warehouse?warehouse=PRE","准备区焊材库",array(
					nav_item::create("material/in?warehouse=PRE","入库","glyphicon glyphicon-log-in"),
					nav_item::create("material/out?warehouse=PRE","出库","glyphicon glyphicon-log-out"),
					nav_item::create("material/store_list?warehouse=PRE","库存","glyphicon glyphicon-home"),
					nav_item::create("material/store_record?warehouse=PRE","进出记录","glyphicon glyphicon-list"),
					nav_item::create("material/sent?warehouse=PRE","焊材发放","glyphicon glyphicon-share"),
					nav_item::create("material/back?warehouse=PRE","焊材回收","glyphicon glyphicon-share-alt"),
					nav_item::create("material/sheet_list?warehouse=PRE","领用单记录","glyphicon glyphicon-list"),
					nav_item::create("material/sheet_list_spot?warehouse=PRE","点口单记录","glyphicon glyphicon-list")
				),"glyphicon glyphicon-home"));



			$this->module["panel/setting"]->child(nav_item::create("panel/common_setting","常规设置",array(
					nav_item::create("setting/basetype","母材类型","glyphicon glyphicon-cog"),
					nav_item::create("setting/basemetal","母材材质","glyphicon glyphicon-cog"),
					nav_item::create("setting/medium","管道介质","glyphicon glyphicon-cog"),
					nav_item::create("setting/jtype","接头型式","glyphicon glyphicon-cog"),
					nav_item::create("setting/gtype","坡口型式","glyphicon glyphicon-cog"),
					nav_item::create("setting/wmethod","焊接方法","glyphicon glyphicon-cog")
				),"glyphicon glyphicon-cog"));

			$this->module["panel/setting"]->child(nav_item::create("panel/material_setting","焊材设置",array(
					nav_item::create("setting/supplier","供应商","glyphicon glyphicon-cog"),
					nav_item::create("setting/wmtype","焊材型号","glyphicon glyphicon-cog"),
					nav_item::create("setting/wmtrademark","焊材牌号","glyphicon glyphicon-cog")
				),"glyphicon glyphicon-cog"));

			$this->module["panel/setting"]->child(nav_item::create("panel/exam_setting","检验设置",array(
					nav_item::create("setting/examrate","检验比例","glyphicon glyphicon-cog"),
					nav_item::create("exam/eps_setting","检验工艺结构","glyphicon glyphicon-cog"),
					nav_item::create("exam/record_setting","检验结果格式","glyphicon glyphicon-cog"),
					nav_item::create("exam/exam_setting","结果额外字段","glyphicon glyphicon-cog")
				),"glyphicon glyphicon-cog"));

			$this->module["panel/alternation"]->child(nav_item::create("panel/alt_data","焊口信息变更",array(
					nav_item::create("alternation/alt_data_add","变更添加","glyphicon glyphicon-list-alt"),
					nav_item::create("alternation/alt_data_check","待审批","glyphicon glyphicon-hourglass"),
					nav_item::create("alternation/alt_data_list","变更清单","glyphicon glyphicon-list")
				),"glyphicon glyphicon-info-sign"));
			$this->module["panel/alternation"]->child(nav_item::create("panel/alt_pressure_test","水压变更",array(
					nav_item::create("alternation/alt_pressure_test_add","变更添加","glyphicon glyphicon-list-alt"),
					nav_item::create("alternation/alt_pressure_test_check","待审批","glyphicon glyphicon-hourglass"),
					nav_item::create("alternation/alt_pressure_test_list","变更清单","glyphicon glyphicon-list")
				),"glyphicon glyphicon-compressed"));
			$this->module["panel/alternation"]->child(nav_item::create("panel/specify_rate","指定检验比例",array(
					nav_item::create("alternation/alt_specify_rate_add","变更添加","glyphicon glyphicon-list-alt"),
					nav_item::create("alternation/alt_specify_rate_check","待审批","glyphicon glyphicon-hourglass"),
					nav_item::create("alternation/alt_specify_rate_list","变更清单","glyphicon glyphicon-list")
				),"glyphicon glyphicon-filter"));


			$this->module["panel/exam"]->child(nav_item::create("panel/exam_tsk","任务",array(
					nav_item::create("exam/tsk_list?emethod=RT","RT任务","glyphicon glyphicon-tasks"),
					nav_item::create("exam/tsk_list?emethod=UT","UT任务","glyphicon glyphicon-tasks"),
					nav_item::create("exam/tsk_list?emethod=PT","PT任务","glyphicon glyphicon-tasks"),
					nav_item::create("exam/tsk_list?emethod=MT","MT任务","glyphicon glyphicon-tasks"),
					nav_item::create("exam/tsk_list?emethod=SA","光谱任务","glyphicon glyphicon-tasks"),
					nav_item::create("exam/tsk_list?emethod=HB","硬度任务","glyphicon glyphicon-tasks"),
					nav_item::create("consignation/consignation_sheet?exam=1","委托单","glyphicon glyphicon-book")
				),"glyphicon glyphicon-tasks"));
			$this->module["panel/exam"]->child(nav_item::create("panel/exam_eps","工艺",array(
					nav_item::create("exam/eps?emethod=RT","RT工艺","glyphicon glyphicon-pawn"),
					nav_item::create("exam/eps?emethod=UT","UT工艺","glyphicon glyphicon-pawn"),
					nav_item::create("exam/eps?emethod=PT","PT工艺","glyphicon glyphicon-pawn"),
					nav_item::create("exam/eps?emethod=MT","MT工艺","glyphicon glyphicon-pawn"),
					nav_item::create("exam/eps?emethod=SA","光谱工艺","glyphicon glyphicon-pawn"),
					nav_item::create("exam/eps?emethod=HB","硬度工艺","glyphicon glyphicon-pawn")
				),"glyphicon glyphicon-pawn"));
			$this->module["panel/exam"]->child(nav_item::create("panel/exam_draft","草稿",array(
					nav_item::create("exam/draft?emethod=RT","RT草稿","glyphicon glyphicon-edit"),
					nav_item::create("exam/draft?emethod=UT","UT草稿","glyphicon glyphicon-edit"),
					nav_item::create("exam/draft?emethod=PT","PT草稿","glyphicon glyphicon-edit"),
					nav_item::create("exam/draft?emethod=MT","MT草稿","glyphicon glyphicon-edit"),
					nav_item::create("exam/draft?emethod=SA","光谱草稿","glyphicon glyphicon-edit"),
					nav_item::create("exam/draft?emethod=HB","硬度草稿","glyphicon glyphicon-edit")
				),"glyphicon glyphicon-edit"));
			$this->module["panel/exam"]->child(nav_item::create("panel/exam_record","结果",array(
					nav_item::create("exam/record?emethod=RT","RT结果","glyphicon glyphicon-briefcase"),
					nav_item::create("exam/record?emethod=UT","UT结果","glyphicon glyphicon-briefcase"),
					nav_item::create("exam/record?emethod=PT","PT结果","glyphicon glyphicon-briefcase"),
					nav_item::create("exam/record?emethod=MT","MT结果","glyphicon glyphicon-briefcase"),
					nav_item::create("exam/record?emethod=SA","光谱结果","glyphicon glyphicon-briefcase"),
					nav_item::create("exam/record?emethod=HB","硬度结果","glyphicon glyphicon-briefcase")
				),"glyphicon glyphicon-briefcase"));
			$this->module["panel/exam"]->child(nav_item::create("panel/exam_report_create","报告出版",array(
					nav_item::create("exam/report_create?emethod=RT","RT报告出版","glyphicon glyphicon-bookmark"),
					nav_item::create("exam/report_create?emethod=UT","UT报告出版","glyphicon glyphicon-bookmark"),
					nav_item::create("exam/report_create?emethod=PT","PT报告出版","glyphicon glyphicon-bookmark"),
					nav_item::create("exam/report_create?emethod=MT","MT报告出版","glyphicon glyphicon-bookmark"),
					nav_item::create("exam/report_create?emethod=SA","光谱报告出版","glyphicon glyphicon-bookmark"),
					nav_item::create("exam/report_create?emethod=HB","硬度报告出版","glyphicon glyphicon-bookmark")
				),"glyphicon glyphicon-bookmark"));
			$this->module["panel/exam"]->child(nav_item::create("panel/exam_report","报告清单",array(
					nav_item::create("exam/report?emethod=RT","RT报告","glyphicon glyphicon-list"),
					nav_item::create("exam/report?emethod=UT","UT报告","glyphicon glyphicon-list"),
					nav_item::create("exam/report?emethod=PT","PT报告","glyphicon glyphicon-list"),
					nav_item::create("exam/report?emethod=MT","MT报告","glyphicon glyphicon-list"),
					nav_item::create("exam/report?emethod=SA","光谱报告","glyphicon glyphicon-list"),
					nav_item::create("exam/report?emethod=HB","硬度报告","glyphicon glyphicon-list")
				),"glyphicon glyphicon-list"));
			$this->module["panel/statistic"]->child(nav_item::create("panel/wj_statistic","焊缝统计",array(
					nav_item::create("statistic/wj_finish","焊缝完成统计","glyphicon glyphicon-stats")
				),"glyphicon glyphicon-stats"));
			$this->module["panel/statistic"]->child(nav_item::create("panel/exam_statistic","检验统计",array(
					nav_item::create("statistic/exam_amount","检验数量统计","glyphicon glyphicon-stats"),
					nav_item::create("statistic/exam_pass_rate","焊口一次合格率","glyphicon glyphicon-stats"),
					nav_item::create("statistic/exam_pass_rate_weight","当量一次合格率","glyphicon glyphicon-stats"),
					nav_item::create("statistic/exam_pass_rate_weight_pp","焊工一次合格率","glyphicon glyphicon-stats")
				),"glyphicon glyphicon-stats"));
			$this->module["panel/statistic"]->child(nav_item::create("panel/material_statistic","材料统计",array(
					nav_item::create("statistic/material_used","焊材用量统计","glyphicon glyphicon-stats"),
					nav_item::create("statistic/material_used_dept","焊材用量统计(分部门)","glyphicon glyphicon-stats"),
					nav_item::create("statistic/material_used_type","焊材用量统计(分型号)","glyphicon glyphicon-stats"),
					nav_item::create("statistic/material_used_trademark","焊材用量统计(分牌号)","glyphicon glyphicon-stats")
				),"glyphicon glyphicon-stats"));
		}

		//$this->module["setting"]->child(nav_item::create("setting/supplier","供应商","setting@supplier"));

		//$this->current_from_url();


		$this->current_controller = $controller;
		$this->current_method = $method;
		$this->current_item = $this->current_item();
		$this->current_module = $this->current_module();
		//dd($this->current_item());
	}


	function add_module(nav_item $nav_item){
		$this->module[$nav_item->tag] = $nav_item;
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
		//在无法搜索到当前节点时，去掉addon后再次搜索
		if ($return === false && strlen($add_on) > 0) {
			foreach ($this->module as $value) {
				$this->item_find($value,function($item){
					if ($item->controller == $this->current_controller."@".$this->current_method) {
						return true;
					}
				},$return);
			}
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
			if (($except === false || ($value->tag != $except && $value->title != $except)) && $value->is_auth()) {
				$data[] = array($value->tag,$value->title,$value->iron);
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
		return $this->current_module->childrens_array();

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
	public $auth = array();
	public $iron = "";
	public $msg;
	
	function __construct($tag,$title,$controller="",$auth=array(),$iron="")
	{
		//设置为可省略controller参数
		if (is_array($controller)) {
			$iron = $auth;
			$auth = $controller;
			$controller = "";
		}
		$this->tag = $tag;
		$this->title = $title;
		$this->auth = $auth;
		$this->iron = $iron;
		if ($controller == "") {
			$this->controller = str_replace("/","@",$tag);
		} else if (substr($this->controller,-1) == "@") {
			$this->controller = $controller.$tag;
		} else {
			$this->controller = $controller;
		}
	}

	public static function create($tag,$title,$controller="",$children_array=array(),$auth=array(),$iron=""){
		//可变参数设置
		if (!is_string($controller) || ($controller != "" && strpos($controller,"@") == false)) {
			$iron = $auth;
			$auth = $children_array;
			$children_array = $controller;
			$controller = "";
		}
		if (sizeof($children_array) > 0 && !$children_array[0] instanceof nav_item) {	
			$iron = $auth;
			$auth = $children_array;
			$children_array = array();
		}
		if (is_string($auth)) {
			$iron = $auth;
			$auth = array();
		}
		if (!is_string($iron)) {
			$iron = "";
		}
		$item = new nav_item($tag,$title,$controller,$auth,$iron);
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
			if ($this->item[$i]->is_auth()) {
				$r_array[] = array($this->item[$i]->tag,$this->item[$i]->title,$this->item[$i]->iron);
			}
		}
		return $r_array;
	}

	function childrens_array(){
		$r_array = array();
		for ($i=0; $i < sizeof($this->item); $i++) { 
			if ($this->item[$i]->is_auth()) {
				$r_array[] = array($this->item[$i]->tag,$this->item[$i]->title,$this->item[$i]->children_array());
			}
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

	function is_auth(){
		if (sizeof($this->auth) == 0) {
			$this->msg = "无须验证";
			return true;
		} else if (in_array(Auth::user()->id,$this->auth)) {
			$this->msg = "授权用户";
			return true;
		} else {
			foreach (multiple_to_array(Auth::user()->auth) as $auth) {
				if (in_array($auth,$this->auth)) {
					$this->msg = "授权组";
					return true;
				}
			}
		}
		$this->msg = "您没有授权";
		return false;
	}
}