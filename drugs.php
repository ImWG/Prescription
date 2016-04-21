<?php
	/*
		��ͼ����drugs
		Ҫʵ�ֵĹ��ܣ�ҩƷ�������ʾ���޸ġ�������ɾ������
		����ͼ��
			ҩƷһ�� - type=listdrugs
			ҩƷ����һ�� - type=listgroups
			�޸�ҩƷ���飨�̶����飩 - type=modify0&notation=*
			�޸�ҩƷ���飨��̬���飩 - type=modify1
			����ҩƷ���飨�̶����飩 - type=create0
			����ҩƷ���飨��̬���飩 - type=create1
	*/
?>
<html>
	<head>
		<meta charset='gbk'>
		<style>
			body, table{
				font-size:9px;
			}
			.box{
				border:solid;
				position:absolute;
				overflow:scroll;
			}
			#box_general{
				width:320px; height:500px; top:0px; left:0px; position:relative;
			}
			#box_left{
				width:320px; height:500px; top:0px; left:0px;
			}
			#box_right{
				width:320px; height:500px; top:0px; left:320px;
			}
			#box_additional{
				width:320px; height:500px; top:0px; left:640px;
			}
			
			.box li:hover{
				background-color:red;
				cursor:hand;
			}
			
			#button_modify{
				position:absolute;
				right:0px;
			}
			#button_remove {
				color:red;
				position:absolute;
				top:50px;
				right:0px;
			}
			#button_resume{
				position:absolute;
				top:100px;
				right:0px;
			}
			#frame_response{
				position:absolute;
				bottom:0px;
				right:0px;
			}
		</style>
	</head>
	<body>
		<?php
			include_once('core/database.php');
			global $DB;
			$DB = new Database();
			$DB->connect();
			
			include('models/drugs.php');
			
			if ($_GET['type']=='listgroups' || $_GET['type']=='' || $_GET['type']==null){
			
				// ************************* �����б�
			
				$groups = Drugs::getGroups();
		?>
				<div class="box" id="box_general">
				<?php
					foreach($groups as $group){
						$groupString = $group['id'].': '.$group['name'].'('.$group['notation'].')';
						echo "<li onclick='location.href=\"drugs.php?type=modify{$group['type']}&notation={$group['notation']}\"'>$groupString</li>";
					}
				?>
				</div>
				<input type='button' value='��ӹ̶���ǩ' onclick='location.href="drugs.php?type=create0"' />
				<input type='button' value='��Ӷ�̬��ǩ' onclick='location.href="drugs.php?type=create1"' />
				<input type='button' value='��ӳ�����ǩ' onclick='location.href="drugs.php?type=create2"' />
				
				<input type='button' id='button_resume' value='����' onclick='location.href="./";'/>
				
		<?php
			}else{
				$function = substr($_GET['type'], 0, -1);
				$type = substr($_GET['type'], -1, 1);
				$notation = $_GET['notation'];
				
				$group;
				if($function=='create'){
					$group = array('id'=>-1);
				}else{
					$group = Drugs::getGroup($notation);
				}
			
				if($type == 0){
				
					// ************************* ����/�޸Ĺ̶�����
					
					
		?>		
				<div class="box" id="box_left">
					<ul id="drug_out_group"><?php
						$drugs = Drugs::getListByGroup($notation, true, true);
						foreach ($drugs as $drug){
							$id = $drug['id'];
							echo "<li value='$id' id='drug_$id' onclick='drugAdd(\"$id\")'>$id: {$drug['name']}</li>";
						}
					?></ul>
				</div>
				<div class="box" id="box_right">
					<ul id="drug_in_group"><?php	
						$drugs = Drugs::getListByGroup($notation, true);
						foreach ($drugs as $drug){
							$id = $drug['id'];
							echo "<li value='$id' id='drug_$id' onclick='drugRemove(\"$id\")'>$id: {$drug['name']}</li>";
						}
					?></ul>
				</div>
				
		<?php
				}else if($type == 1){
				
					// ************************* ����/�޸Ķ�̬����
					
					$notation = $_GET['notation'];
		?>
				<div class="box" id="box_left">
					<ul id="drug_out_group"><?php
						$drugs = Drugs::getListByGroup($notation, true);
						foreach ($drugs as $drug){
							$id = $drug['id'];
							echo "<li value='$id' id='drug_$id'>$id: {$drug['name']}</li>";
						}
					?></ul>
				</div>
				
				<div class="box" id="box_right">

				</div>
				
		<?php
				}else if($type == 2){
				
					// ************************* ����/�޸ĳ�������
					
					$notation = $_GET['notation'];
		?>
				<div class="box" id="box_left">
					<ul id="drug_out_group"><?php
						$drugs = Drugs::getGroupsByGroup($notation, true);
						foreach ($drugs as $drug){
							$id = $drug['id'];
							echo "<li value='{$drug['notation']}' id='drug_$id' onclick='drugAdd(\"$id\")'>$id: {$drug['name']}({$drug['notation']})</li>";
						}
					?></ul>
				</div>
				<div class="box" id="box_right">
					<ul id="drug_in_group"><?php	
						$drugs = Drugs::getGroupsByGroup($notation);
						foreach ($drugs as $drug){
							$id = $drug['id'];
							echo "<li value='{$drug['notation']}' id='drug_$id' onclick='drugRemove(\"$id\")'>$id: {$drug['name']}({$drug['notation']})</li>";
						}
					?></ul>
				</div>
				
			<?php
				}
			?>
				<div class="box" id="box_additional">
					<p>��ţ�<?php echo $group['id']; ?></p>
					<p>���ţ�<input type='text' id='group_notation' value='<?php echo $group['notation']; ?>'/></p>
					<p>���ƣ�<input type='text' id='group_name' value='<?php echo $group['name']; ?>'/></p>
					<p>��ע��<textarea id='group_memo'><?php echo $group['memo']; ?></textarea></p>
					<?php
						//��̬��ǩ����д����
						if ($type == 1){
							$tData = explode("\0", $group['data']);
							$group['column'] = $tData[0];
							$group['condition'] = '';
							for ($i=1; $i<count($tData);++$i){
								$group['condition'] .= ' '.$tData[$i];
							}
							$group['condition'] = substr($group['condition'], 1);
							
							echo '<p>----------------------------</p>';
							
							//echo "<p>�ֶ�����<input type='text' id='group_column' value='{$group['column']}'/></p>";
							echo '<p>�ֶ�����<select type="text" id="group_column"/>';
							echo '<option value="_">(��)</option>';
							foreach (Drugs::$COLUMNS_DYNAMIC_GROUP as $KEY => $NAME){
								$selected = ($group['column']==$KEY ? 'selected="true"' : 'false');
								echo "<option value='$KEY' $selected>{$NAME}({$KEY})</option>";
							}
							echo '</select></p>';
							echo "<p>�ؼ��֣�<textarea id='group_condition'>{$group['condition']}</textarea></p>";
							echo "<p>��ͬ�ؼ���֮���ÿո�ֿ����ؼ���ǰ��ӡ�%�����ʾǰ������������֡�</p>";
						}
					?>
				</div>
				
				<form method="post" action="p_drugs.php?type=modify" id='my_form' target='frame_response'>
					
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
					<input type='hidden' name='type' value='<?php echo $type; ?>'/>
					<span id='form_drugs'></span>
				</form>
				<form method="post" action="p_drugs.php?type=remove" id='my_form_remove' target='frame_response'>
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
				</form>
				<input type='button' id='button_modify' value='�޸�' onclick='submitDrugGroup<?php echo $type; ?>();'/>
			<?php if( $function == 'modify' ){ ?>
				<input type='button' id='button_remove' value='ɾ��' onclick='submitDrugGroupRemove();'/>
			<?php } ?>
				<input type='button' id='button_resume' value='����' onclick='location.href="drugs.php?type=listgroups";'/>
				<iframe id='frame_response' name='frame_response' sandbox=''></iframe>
				
		<?php
			}
		?>
		
	</body>
	<script src="./js/drugs.js"></script>
</html>