<?php
	$DATABASE_HOST = 'localhost';
	$DATABASE_USERNAME = 'root';
	$DATABASE_PASSWORD = '';
	
	$DATABASE_NAME = 'presc_eval';
	
	
	$COLUMNS_PRESCS = array(
		'Id'=>'登记号',
		'Prescription'=>'处方号',
		'PatientName'=>'病人姓名',
		'PatientGender'=>'病人性别',
		'PatientAge'=>'病人年龄',
		'Doctor'=>'医生姓名',
		'Service'=>'科室',
		'Diagnosis'=>'诊断',);
		
	$COLUMNS_ITEMS = array(
		'Id'=>'登记号',
		'Prescription'=>'处方号',
		'ItemId'=>'项目号',
		'Name'=>'通用名',
		'AdviceCategory'=>'医嘱类型',
		'Scale'=>'药品/规格',
		'Method'=>'用法',
		'Frequency'=>'频次',
		'Dosage'=>'剂量',
		'Time'=>'医嘱时间',
		'Level'=>'质量层次',
		'Insurance'=>'医保属性',
		'Price'=>'单价');
	
	/*
		您可以修改其中的属性，来添加/删除评判标准
		EReserved1~EReserved4是保留项目，可以添加以作为补充。原有的项目也可以修改名称。
		不要删除“项目号”和“已阅”
	*/
	$COLUMNS_EVALS = array(
		'ItemId'=>'项目号',
		
		'EMatch'=>'诊断与用药是否相符',
		'EDosage'=>'给药剂量是否适宜',
		'EFrequency'=>'给药频次是否适宜',
		'EOccasion'=>'给药时机是否适宜',
		'ECourse'=>'疗程是否适宜',
		'ECombine'=>'联合用药是否适宜',
		
		'EOther'=>'备注',
		'Checked'=>'已阅'
	);
?>