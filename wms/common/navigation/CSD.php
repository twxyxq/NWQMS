<?php


$this->add_module(nav_item::create("panel/cqcn_own","资质管理",array("wechat"),"glyphicon glyphicon-stats"));

$this->module["panel/cqcn_own"]->child(nav_item::create("panel/cqcn","无损检测资质",array(
		nav_item::create("pp/cqcn_del","证书添加"),
		nav_item::create("pp/cqcn_list","我的证书"),
		nav_item::create("pp/cqcn_list_all","证书列表"),
		nav_item::create("pp/cqcn_plan_manager","考证计划管理",array("wechat_manager")),
		nav_item::create("pp/cqcn_plan","考证计划填报")
	)));



$this->add_module(nav_item::create("panel/radiation_gps","放射源管理",array("wechat"),"glyphicon glyphicon-stats"));

$this->module["panel/radiation_gps"]->child(nav_item::create("panel/equipment","设备管理",array(
		nav_item::create("radiation_gps/equipment_name","设备别名")
	)));

$this->module["panel/radiation_gps"]->child(nav_item::create("radiation_gps/gps","设备定位"));
