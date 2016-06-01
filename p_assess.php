<?php
	include_once('core/util.php');
	Util::startTimer();

	header("Content-type: text; charset=gbk");

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
	
	include_once('models/drugs.php');
	include_once('models/equals.php');
	include_once('models/assess.php');
	
	$result = Assess::assessDrug(Util::arrayIconvUTF82GBK($_POST));
	echo json_encode(Util::arrayIconvGBK2UTF8($result));
	//print_r($_POST);
	
	
	Util::endTimer(false);
?>