<?php

define("PJCODE","1625");

define("ILD", "0,1,2,3,4,5,6,7,8,9");

define("PROJECT", "核岛安装,常规岛安装,BOP安装,核岛土建");

//定义检验比例常量
define("SQL_EXAM_RATE","CONCAT(IF(RT=0,'',CONCAT('RT:',RT,';')),IF(UT=0,'',CONCAT('UT:',UT,';')),IF(PT=0,'',CONCAT('PT:',PT,';')),IF(MT=0,'',CONCAT('MT:',MT,';')))");
//定义焊缝号常量
define("SQL_VCODE","IF(CONCAT(ild,sys,'-',pipeline,'-',vnum)=vcode,vcode,CONCAT(vcode,' [',ild,sys,'-',pipeline,'-',vnum,']'))");
//定义材质常量
//define("SQL_BASE_METAL","CONCAT(ac,IF((at=bt AND ath=bth),'',CONCAT(' Φ',at,'×',ath)),IF(ac=bc,' ',CONCAT('/',bc,' ')),'Φ',bt,'×',bth)");
define("SQL_BASE_C","CONCAT(ac,IF(ac=bc,'',CONCAT('/',bc)))");

define("SQL_BASE_A","IF(CHAR_LENGTH(a_alias)>0,a_alias,IF(at=0,CONCAT('t',ath,'mm'),CONCAT('Φ',at,'×',ath)))");
define("SQL_BASE_B","IF(CHAR_LENGTH(b_alias)>0,b_alias,IF(bt=0,CONCAT('t',bth,'mm'),CONCAT('Φ',bt,'×',bth)))");

define("SQL_BASE_TYPE","IF(a_alias=b_alias AND at=bt AND ath=bth,".SQL_BASE_A.",CONCAT(".SQL_BASE_A.",'/',".SQL_BASE_B."))");

define("SQL_BASE","IF(ac=bc,CONCAT(ac,' ',".SQL_BASE_TYPE."),IF(a_alias=b_alias AND at=bt AND ath=bth,CONCAT(ac,'/',bc,' ',".SQL_BASE_A."),CONCAT(ac,' ',".SQL_BASE_A.",'/',bc,' ',".SQL_BASE_B.")))");


//define("SQL_BASE_TYPE_STRUCTURE","IF(at=0 AND bt=0,CONCAT('t',ath,'mm'),IF(at=0,CONCAT('t',ath,'mm/Φ',bt,'×',bth),CONCAT('Φ',at,'×',bth,'/t',bth,'mm')))");
//define("SQL_BASE_TYPE","IF(at=0 OR bt=0,".SQL_BASE_TYPE_STRUCTURE.",CONCAT('Φ',at,'×',ath,IF(CONCAT(at,ath)=CONCAT(bt,bth),'',CONCAT('/Φ',bt,'×',bth))))");
//define("SQL_BASE","IF(ac=bc,CONCAT(ac,' ',".SQL_BASE_TYPE."),IF(at=bt and ath=bth,CONCAT(ac,'/',bc,' ','Φ',at,'×',ath),CONCAT(ac,' ','Φ',at,'×',ath,'/',bc,' ','Φ',bt,'×',bth)))");


define("SQL_ST_BASE_C","IF(st_ac=st_bc,st_ac,CONCAT(st_ac,' ',st_bc))");
define("SQL_ST_BASE_TYPE","IF(st_at='N/A',IF(st_ath=st_bth,CONCAT(st_ath,'mm'),CONCAT(st_ath,'mm/',st_bth,'mm')),CONCAT('Φ',st_at,'×',st_ath,IF(CONCAT(st_at,st_ath)=CONCAT(st_bt,st_bth),'',CONCAT('/Φ',st_bt,'×',st_bth))))");

define("SQL_ST_BASE","CONCAT(".SQL_ST_BASE_C.",' ',".SQL_ST_BASE_TYPE.")");


//授权信息

define("AUTH_EXAM_PLAN_CANCEL","weld_qc3");
define("AUTH_EXAM_REPORT_CANCEL","weld_qc3");
define("AUTH_EXAM_SHEET_CANCEL","weld_qc3");