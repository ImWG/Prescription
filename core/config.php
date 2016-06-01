<?php
	$DATABASE_HOST = 'localhost';
	$DATABASE_USERNAME = 'root';
	$DATABASE_PASSWORD = '';
	
	$DATABASE_NAME = 'presc_eval';
	
	
	$COLUMNS_PRESCS = array(
		'Id'=>'�ǼǺ�',
		'Prescription'=>'������',
		'PatientName'=>'��������',
		'PatientGender'=>'�����Ա�',
		'PatientAge'=>'��������',
		'Doctor'=>'ҽ������',
		'Service'=>'����',
		'Diagnosis'=>'���',);
		
	$COLUMNS_ITEMS = array(
		'Id'=>'�ǼǺ�',
		'Prescription'=>'������',
		'ItemId'=>'��Ŀ��',
		'DrugId'=>'ҩƷ��',
		'Name'=>'ͨ����',
		'AdviceCategory'=>'ҽ������',
		'Scale'=>'ҩƷ/���',
		'Method'=>'�÷�',
		'Frequency'=>'Ƶ��',
		'Dosage'=>'����',
		'Time'=>'ҽ��ʱ��',
		'Level'=>'�������',
		'Insurance'=>'ҽ������',
		'Price'=>'����');
	
	/*
		�������޸����е����ԣ������/ɾ�����б�׼
		EReserved1~EReserved4�Ǳ�����Ŀ�������������Ϊ���䡣ԭ�е���ĿҲ�����޸����ơ�
		��Ҫɾ������Ŀ�š��͡����ġ�
	*/
	$COLUMNS_EVALS = array(
		'ItemId'=>'��Ŀ��',
		
		'EMatch'=>'�������ҩ�Ƿ����',
		'EDosage'=>'��ҩ�����Ƿ�����',
		'EFrequency'=>'��ҩƵ���Ƿ�����',
		'EOccasion'=>'��ҩʱ���Ƿ�����',
		'ECourse'=>'�Ƴ��Ƿ�����',
		'ECombine'=>'������ҩ�Ƿ�����',
		
		'EOther'=>'��ע',
		'Checked'=>'����'
	);
	
	//ÿ�еĿ�ȣ��뱣֤ÿһ�ж�Ϊ12
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