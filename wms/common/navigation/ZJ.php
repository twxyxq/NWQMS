<?php

$this->add_module(nav_item::create("panel/pp","人员","glyphicon glyphicon-user"));
			
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