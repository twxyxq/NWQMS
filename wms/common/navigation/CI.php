<?php

$this->add_module(nav_item::create("panel/wj","焊口","glyphicon glyphicon-info-sign"));
$this->add_module(nav_item::create("panel/weld","焊接",array("weld_syn"),"glyphicon glyphicon-baby-formula"));
$this->add_module(nav_item::create("panel/pp","人员","glyphicon glyphicon-user"));
$this->add_module(nav_item::create("panel/material","材料",array("weld_syn","m_LOC","m_PRE"),"glyphicon glyphicon-oil"));
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
		nav_item::create("material/sheet_list_unsent","领用单未发放","glyphicon glyphicon-lamp"),
		nav_item::create("material/sheet_list_unback","领用单未回收","glyphicon glyphicon-tent"),
		nav_item::create("material/sheet_list_spot","点口单列表","glyphicon glyphicon-list"),
		nav_item::create("material/sheet_list_spot_unsent","点口单未发放","glyphicon glyphicon-lamp"),
		nav_item::create("material/sheet_list_spot_unback","点口单未回收","glyphicon glyphicon-tent")
	),array("weld_syn"),"glyphicon glyphicon-folder-close"));
$this->module["panel/material"]->child(nav_item::create("panel/warehouse?warehouse=LOC","现场焊材库",array(
		nav_item::create("material/in?warehouse=LOC","入库","glyphicon glyphicon-log-in"),
		nav_item::create("material/out?warehouse=LOC","出库","glyphicon glyphicon-log-out"),
		nav_item::create("material/store_list?warehouse=LOC","库存","glyphicon glyphicon-home"),
		nav_item::create("material/store_record?warehouse=LOC","进出记录","glyphicon glyphicon-list"),
		nav_item::create("material/sent?warehouse=LOC","焊材发放","glyphicon glyphicon-share"),
		nav_item::create("material/back?warehouse=LOC","焊材回收","glyphicon glyphicon-share-alt"),
		nav_item::create("material/sheet_list?warehouse=LOC","领用单记录","glyphicon glyphicon-list"),
		nav_item::create("material/sheet_list_spot?warehouse=LOC","点口单记录","glyphicon glyphicon-list")
	),array("m_LOC"),"glyphicon glyphicon-home"));
$this->module["panel/material"]->child(nav_item::create("panel/warehouse?warehouse=PRE","准备区焊材库",array(
		nav_item::create("material/in?warehouse=PRE","入库","glyphicon glyphicon-log-in"),
		nav_item::create("material/out?warehouse=PRE","出库","glyphicon glyphicon-log-out"),
		nav_item::create("material/store_list?warehouse=PRE","库存","glyphicon glyphicon-home"),
		nav_item::create("material/store_record?warehouse=PRE","进出记录","glyphicon glyphicon-list"),
		nav_item::create("material/sent?warehouse=PRE","焊材发放","glyphicon glyphicon-share"),
		nav_item::create("material/back?warehouse=PRE","焊材回收","glyphicon glyphicon-share-alt"),
		nav_item::create("material/sheet_list?warehouse=PRE","领用单记录","glyphicon glyphicon-list"),
		nav_item::create("material/sheet_list_spot?warehouse=PRE","点口单记录","glyphicon glyphicon-list")
	),array("m_PRE"),"glyphicon glyphicon-home"));



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

$this->module["panel/alternation"]->child(nav_item::create("panel/alternation_wj","焊口及任务",array(
		nav_item::create("panel/cancel","焊口作废",array(
			nav_item::create("alternation/cancel_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]cancel_procedure|wj","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/cancel_procedure|wj","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-trash"),
		nav_item::create("panel/alt_data","焊口变更",array(
			nav_item::create("alternation/alt_data_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/alt_data_all_add","变更添加(S)",array("weld_manager"),"glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]alt_procedure|wj","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/alt_procedure|wj","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-info-sign"),
		nav_item::create("panel/tsk_recovery","任务恢复",array(
			nav_item::create("alternation/tsk_recovery_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]tsk_recovery|tsk","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/tsk_recovery|tsk","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon glyphicon-refresh")
	),"glyphicon glyphicon-bullhorn"));
$this->module["panel/alternation"]->child(nav_item::create("panel/alternation_exam","委托及检验",array(
		nav_item::create("panel/alt_pressure_test","水压变更",array(
			nav_item::create("alternation/alt_pressure_test_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]alt_pressure_test_procedure|wj","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/alt_pressure_test_procedure|wj","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-compressed"),
		nav_item::create("panel/specify_rate","指定检验比例",array(
			nav_item::create("alternation/alt_specify_rate_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]alt_exam_specify_procedure|wj","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/alt_exam_specify_procedure|wj","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-filter"),
		nav_item::create("panel/report_cancel","报告撤销",array(
			nav_item::create("alternation/cancel_report_procedure_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]cancel_report_procedure|exam_report","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/cancel_report_procedure|exam_report","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-modal-window"),
		nav_item::create("panel/exam_plan_cancel","检验组撤销",array(
			nav_item::create("alternation/cancel_exam_plan_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]cancel_exam_plan_procedure|exam_plan","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/cancel_exam_plan_procedure|exam_plan","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-object-align-vertical"),
		nav_item::create("panel/exam_sheet_cancel","委托单撤销",array(
			nav_item::create("alternation/cancel_exam_sheet_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]cancel_exam_sheet_procedure|exam_sheet","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/cancel_exam_sheet_procedure|exam_sheet","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-erase"),
		nav_item::create("panel/exam_sheet_modify","委托单修改",array(
			nav_item::create("alternation/modify_exam_sheet_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]modify_exam_sheet_procedure|exam_sheet","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/modify_exam_sheet_procedure|exam_sheet","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-edit")
	),"glyphicon glyphicon-bell"));
$this->module["panel/alternation"]->child(nav_item::create("panel/alternation_material","焊材",array(
		nav_item::create("panel/alt_material_sheet","领用单变更",array(
			nav_item::create("alternation/alt_material_sheet_add","变更添加","glyphicon glyphicon-list-alt"),
			nav_item::create("alternation/[c]alt_procedure|material_sheet","待审批","glyphicon glyphicon-hourglass"),
			nav_item::create("alternation/alt_procedure|material_sheet","变更清单","glyphicon glyphicon-list")
		),"glyphicon glyphicon-file")
	),"glyphicon glyphicon-file"));


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