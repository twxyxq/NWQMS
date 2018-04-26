<?php


$this->add_module(nav_item::create("panel/cqcn_own","资质管理",array("wechat"),"glyphicon glyphicon-stats"));

$this->module["panel/cqcn_own"]->child(nav_item::create("panel/cqcn","无损检测资质",array(
		nav_item::create("pp/cqcn_del","证书添加","glyphicon glyphicon-pencil"),
		nav_item::create("pp/cqcn_list","我的证书","glyphicon glyphicon-inbox"),
		nav_item::create("pp/cqcn_list_all","证书列表","glyphicon glyphicon-th-list"),
		nav_item::create("pp/cqcn_plan_manager","考证计划管理",array("wechat_manager")),
		nav_item::create("pp/cqcn_plan","考证计划填报","glyphicon glyphicon-tasks")
	),"glyphicon glyphicon-book"));

$this->module["panel/cqcn_own"]->child(nav_item::create("panel/pp_qf","HAF焊工资质",array(
		nav_item::create("pp/pp_scan_personal","授权证书扫描")
	),array(1)));

//*********************************************************************************************

$this->add_module(nav_item::create("panel/radiation_gps","放射源管理",array("wechat"),"glyphicon glyphicon-screenshot"));

$this->module["panel/radiation_gps"]->child(nav_item::create("panel/equipment","设备管理",array(
		nav_item::create("radiation_gps/equipment_name","设备别名")
	),"glyphicon glyphicon-exclamation-sign"));

$this->module["panel/radiation_gps"]->child(nav_item::create("radiation_gps/gps","设备定位","glyphicon glyphicon-screenshot"));

//*********************************************************************************************

$this->add_module(nav_item::create("panel/lock_ai","智能锁",array("wechat"),"glyphicon glyphicon-lock"));

$this->module["panel/lock_ai"]->child(nav_item::create("panel/lock_ai_add","锁管理",array(
		nav_item::create("lock_ai/lock_ai_add","锁添加")
	),"glyphicon glyphicon-lock"));

$this->module["panel/lock_ai"]->child(nav_item::create("panel/lock_ai_auth","开锁授权",array(
		nav_item::create("lock_ai/auth","申请和更新"),
		nav_item::create("lock_ai/auth_list","授权清单","glyphicon glyphicon-tags")
	),"glyphicon glyphicon-tower"));

//*********************************************************************************************	

$this->add_module(nav_item::create("panel/Interior_Management","内业管理",array("wechat"),"glyphicon glyphicon-book"));

$this->module["panel/Interior_Management"]->child(nav_item::create("panel/account_book","内部账本",array(
		nav_item::create("interior_management/account_book_list","账本清单","glyphicon glyphicon-th-list")
	),"glyphicon glyphicon-credit-card"));

$this->module["panel/Interior_Management"]->child(nav_item::create("panel/work_report","工作汇报",array(
		nav_item::create("interior_management/current_report","汇报详情","glyphicon glyphicon-th-list"),
		nav_item::create("interior_management/my_report","我的汇报","glyphicon glyphicon-bookmark")
	),"glyphicon glyphicon-blackboard"));

$this->module["panel/Interior_Management"]->child(nav_item::create("panel/overtime","内部考勤",array(
		nav_item::create("interior_management/overtime_personal","个人考勤","glyphicon glyphicon-user"),
		nav_item::create("interior_management/overtime_examine_and_approve","考勤审批","glyphicon glyphicon-thumbs-up"),
		nav_item::create("interior_management/overtime_statistic","考勤统计","glyphicon glyphicon-user")
	),"glyphicon glyphicon-stats"));


$this->module["panel/Interior_Management"]->child(nav_item::create("panel/photo_store","照片归档",array(
		nav_item::create("interior_management/all_photo","全部照片","glyphicon glyphicon-picture"),
		nav_item::create("interior_management/photo_manager","照片管理","glyphicon glyphicon-folder-close")
	),"glyphicon glyphicon-blackboard"));