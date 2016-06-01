<?php
	/*
		��ͼ����equals
		Ҫʵ�ֵĹ��ܣ���Ч���������ʾ���޸ġ�������ɾ������
		������
			�޸ĵ�Ч������Ĵ��� - notation
	*/
?>
<html>
	<head>
		<meta charset='gbk'>
		<link href="./css/drugs.css" rel="stylesheet">
	</head>
	<body>
		<?php
			include_once('core/database.php');
			global $DB;
			$DB = new Database();
			$DB->connect();
						
			include('models/equals.php');
			$currentEquals = new DiseaseEquals();
			
			$table = $_GET['table'];
			//��ѡ������
			if ($table == 'disease'){
				$currentEquals = new DiseaseEquals();
			}else if ($table == 'combine'){
				$currentEquals = new CombineEquals();
			}
		
			// ************************* �����б�
		
			$groups = $currentEquals->getGroups();
		?>
			<div class="box" id="box_left">
			<?php
				foreach($groups as $group){
					$groupString = $group['id'].': '.$group['name'].'('.$group['notation'].')';
					echo "<li onclick='location.href=\"equals.php?table={$table}&notation={$group['notation']}\"'>$groupString</li>";
				}
			?>
			</div>
				
		<?php
			$notation = $_GET['notation'];
			$function = ($notation == null || $notation == '') ? 'create' : 'modify';
			
			$group;
			if($function=='create'){
				$group = array('id'=>-1, 'type'=>0);
			}else{
				$group = $currentEquals->getGroup($notation);
			}
			
			$type = $group['type'];
			
			// ************************* ����/�޸�
		?>
				
			<div class="box" id="box_right">
				<span id='t_list'></span>
			</div>
			
			<div class="box" id="box_additional">
			<?php 
				if( $table == 'disease' ){
					$labels = array('id'=>'���','notation'=>'����', 'name'=>'����', 'condition'=>'����', 'column'=>'��֢��ǩ', 'memo'=>'��ע');
				}elseif ($table == 'combine') {
					$labels = array('id'=>'���','notation'=>'����', 'name'=>'����', 'condition'=>'��ͻҩƷ', 'column'=>'', 'memo'=>'��ע');
				}
			?>
				<p><?php echo $labels['id'];?>��<?php echo $group['id']; ?></p>
				<p><?php echo $labels['notation'];?>��<input type='text' id='group_notation' value='<?php echo $group['notation']; ?>'/></p>
				<p><?php echo $labels['name'];?>��<input type='text' id='group_name' value='<?php echo $group['name']; ?>'/></p>
				<?php
					//��д����
					$tData = explode("\0", $group['data']);
					$group['condition'] = implode(' ', $tData);
					
					echo '<p>----------------------------</p>';
					
					echo "<p>{$labels['condition']}��<textarea id='group_condition'>{$group['condition']}</textarea></p>";
					echo "<p>��ͬ{$labels['condition']}֮���ÿո�ֿ���</p>";
					
					if( $table == 'disease' ){
				?>
				<p><?php echo $labels['column'];?>��<input id='group_column' value='<?php echo $group['drugs']; ?>' />
				<?php }?>
				
				<p><?php echo $labels['memo'];?>��<textarea id='group_memo'><?php echo $group['memo']; ?></textarea></p>
			</div>
			
			<form method="post" action="p_equals.php?table=<?php echo $table;?>&type=modify" id='my_form' target='frame_response'>
				<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
				<input type='hidden' name='type' value='<?php echo $type; ?>'/>
				<span id='form_drugs'></span>
			</form>
			<form method="post" action="p_equals.php?table=<?php echo $table;?>&type=remove" id='my_form_remove' target='frame_response'>
				<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
			</form>
			<input type='button' id='button_modify' value='�޸�' onclick='submitDrugGroup1();'/>
			
		<?php if( $function == 'modify' ){ ?>
			<input type='button' id='button_remove' value='ɾ��' onclick='submitDrugGroupRemove();'/>
			<input type='button' id='button_resume' value='����' onclick='location.href="equals.php?table=<?php echo $table;?>";'/>
		<?php } else {?>
			<input type='button' id='button_resume' value='����' onclick='location.href=".";'/>
		<?php } ?>
		
			<iframe id='frame_response' name='frame_response' sandbox=''></iframe>
		<?php if ($table == 'disease') {?>
			<input type='button' id='button_other1' value='�г�ҩƷ����' onclick='loadDrugGroups();'/>
		<?php }elseif ($table == 'combine') { ?>
			<div id='button_other1'>
				<input type='button' value='�г�ҩƷ' onclick='loadDrugsForEquals();'/>
				<input type='button' value='�г���ѡҩƷ' onclick='loadDrugsForEqualsByIds("group_condition");'/>
			</div>
		<?php } ?>
		
	</body>
	<script src="./js/drugs.js"></script>
	<script src="http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js"></script>
</html>