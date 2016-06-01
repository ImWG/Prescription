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
		'DrugId'=>'药品名',
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
	
	//每列的宽度，请保证每一行都为12
	$COLUMNS_STRIDE = array(
		'P'=>array(
			'Id'=>2,
			'Prescription'=>2,
			'PatientName'=>3,
			'PatientGender'=>2,
			'PatientAge'=>3,
			'Doctor'=>2,
			'Service'=>2,
			'Diagnosis'=>8
		),
		
		'I'=>array(
			'Id'=>12,
			'Prescription'=>12,
			
			'ItemId'=>2,
			'DrugId'=>2,
			'Name'=>5,
			'AdviceCategory'=>3,
			'Scale'=>6,
			'Method'=>2,
			'Frequency'=>2,
			'Dosage'=>2,
			'Time'=>3,
			'Level'=>3,
			'Insurance'=>3,
			'Price'=>3,
		),
		
		'E'=>array(
			'ItemId'=>12,
			
			'EMatch'=>4,
			'EDosage'=>4,
			'EFrequency'=>4,
			'EOccasion'=>4,
			'ECourse'=>4,
			'ECombine'=>4,
			
			'EOther'=>10,
			'Checked'=>2
		)
	);
?>