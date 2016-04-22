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
			
				// ************************* �����б�
			
				$groups = Equals::getGroups();
		?>
				<div class="box" id="box_left">
				<?php
					foreach($groups as $group){
						$groupString = $group['id'].': '.$group['name'].'('.$group['notation'].')';
						echo "<li onclick='location.href=\"equals.php?notation={$group['notation']}\"'>$groupString</li>";
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
					$group = Equals::getGroup($notation);
				}
				
				$type = $group['type'];
				
				// ************************* ����/�޸Ķ�̬����
		?>
				
				<div class="box" id="box_right">

				</div>
				
				<div class="box" id="box_additional">
					<p>��ţ�<?php echo $group['id']; ?></p>
					<p>���ţ�<input type='text' id='group_notation' value='<?php echo $group['notation']; ?>'/></p>
					<p>���ƣ�<input type='text' id='group_name' value='<?php echo $group['name']; ?>'/></p>
					<?php
						//��̬��ǩ����д����
						
							$tData = explode("\0", $group['data']);
							$group['condition'] = implode(' ', $tData);
							
							echo '<p>----------------------------</p>';
							
							echo "<p>������<textarea id='group_condition'>{$group['condition']}</textarea></p>";
							echo "<p>��ͬ����֮���ÿո�ֿ����ؼ���ǰ��ӡ�%�����ʾǰ������������֡�</p>";
						
					?>
					<p>��ע��<textarea id='group_memo'><?php echo $group['memo']; ?></textarea></p>
				</div>
				
				<input type='hidden' id='group_column' value=''/>
				<form method="post" action="p_equals.php?type=modify" id='my_form' target='frame_response'>
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
					<input type='hidden' name='type' value='<?php echo $type; ?>'/>
					<span id='form_drugs'></span>
				</form>
				<form method="post" action="p_equals.php?type=remove" id='my_form_remove' target='frame_response'>
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
				</form>
				<input type='button' id='button_modify' value='�޸�' onclick='submitDrugGroup1();'/>
			<?php if( $function == 'modify' ){ ?>
				<input type='button' id='button_remove' value='ɾ��' onclick='submitDrugGroupRemove();'/>
				<input type='button' id='button_resume' value='����' onclick='location.href="equals.php";'/>
			<?php } else {?>
				<input type='button' id='button_resume' value='����' onclick='location.href=".";'/>
			<?php } ?>
				<iframe id='frame_response' name='frame_response' sandbox=''></iframe>
		
	</body>
	<script src="./js/drugs.js"></script>
</html>