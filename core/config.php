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
?>