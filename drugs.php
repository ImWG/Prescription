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
			
			if ($_GET['type']=='listgroups' || $_GET['type']==''){
			
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
				
		<?php
			}else if($_GET['type']=='modify0' || $_GET['type']=='create0'){
			
				// ************************* ����/�޸Ĺ̶�����
				
				$notation = $_GET['notation'];
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
				<div class="box" id="box_additional">
					<?php
						$group;
						if($_GET['type']=='create0'){
							$group = array('id'=>-1);
						}else{
							$group = Drugs::getGroup($notation);
						}
					?>
					<p>��ţ�<?php echo $group['id']; ?></p>
					<p>���ţ�<input type='text' id='group_notation' value='<?php echo $group['notation']; ?>'/></p>
					<p>���ƣ�<input type='text' id='group_name' value='<?php echo $group['name']; ?>'/></p>
					<p>��ע��<textarea id='group_memo'><?php echo $group['memo']; ?></textarea></p>
				</div>
				
				<form method="post" action="p_drugs.php?type=modify" id='my_form' target='frame_response'>
					
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
					<input type='hidden' name='type' value='0'/>
					<span id='form_drugs'></span>
				</form>
				<form method="post" action="p_drugs.php?type=remove" id='my_form_remove' target='frame_response'>
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
				</form>
				<input type='button' id='button_modify' value='�޸�' onclick='submitDrugGroup0();'/>
			<?php if($_GET['type']=='modify0'){ ?>
				<input type='button' id='button_remove' value='ɾ��' onclick='submitDrugGroupRemove();'/>
			<?php } ?>
				<input type='button' id='button_resume' value='����' onclick='location.href="drugs.php?type=listgroups";'/>
				<iframe id='frame_response' name='frame_response' sandbox=''></iframe>
		<?php
			}else if($_GET['type']=='modify1' || $_GET['type']=='create1'){
			
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
					<?php
						$group;
						if($_GET['type']=='create1'){
							$group = array('id'=>-1);
						}else{
							$group = Drugs::getGroup($notation);
						}
						$tData = explode("\0", $group['data']);
						$group['column'] = $tData[0];
						$group['condition'] = '';
						for ($i=1; $i<count($tData);++$i){
							$group['condition'] .= ' '.$tData[$i];
						}
						$group['condition'] = substr($group['condition'], 1);
					?>
					<input type='text' id='group_column' value='<?php echo $group['column']; ?>'/>
					<textarea id='group_condition'><?php echo $group['condition']; ?></textarea>
				</div>
				
				<div class="box" id="box_additional">
					<p>��ţ�<?php echo $group['id']; ?></p>
					<p>���ţ�<input type='text' id='group_notation' value='<?php echo $group['notation']; ?>'/></p>
					<p>���ƣ�<input type='text' id='group_name' value='<?php echo $group['name']; ?>'/></p>
					<p>��ע��<textarea id='group_memo'><?php echo $group['memo']; ?></textarea></p>
				</div>
				
				<form method="post" action="p_drugs.php?type=modify" id='my_form' target='frame_response'>
					
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
					<input type='hidden' name='type' value='1'/>
					<span id='form_drugs'></span>
				</form>
				<form method="post" action="p_drugs.php?type=remove" id='my_form_remove' target='frame_response'>
					<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
				</form>
				<input type='button' id='button_modify' value='�޸�' onclick='submitDrugGroup1();'/>
			<?php if($_GET['type']=='modify1'){ ?>
				<input type='button' id='button_remove' value='ɾ��' onclick='submitDrugGroupRemove();'/>
			<?php } ?>
				<input type='button' id='button_resume' value='����' onclick='location.href="drugs.php?type=listgroups";'/>
				<iframe id='frame_response' name='frame_response' sandbox=''></iframe>
		<?php
			}
		?>
		
	</body>
	<script>
		function drugAdd(id){
			var drug = document.getElementById('drug_'+id);
			drug.setAttribute('onclick','drugRemove("'+id+'")');
			var drugStr = drug.outerHTML;
			drug.outerHTML = '';
			
			var drugInGroup = document.getElementById('drug_in_group');
			drugInGroup.innerHTML += drugStr;
		}
		
		function drugRemove(id){
			var drug = document.getElementById('drug_'+id);
			drug.setAttribute('onclick','drugAdd("'+id+'")');
			var drugStr = drug.outerHTML;
			drug.outerHTML = '';
			
			var drugOutGroup = document.getElementById('drug_out_group');
			drugOutGroup.innerHTML += drugStr;
		}
		
		function submitDrugGroupMeta(formDrugs){
			formDrugs.innerHTML += '<input type="hidden" name="name" value="'+document.getElementById('group_name').value+'" />';
			formDrugs.innerHTML += '<input type="hidden" name="notation" value="'+document.getElementById('group_notation').value+'" />';
			formDrugs.innerHTML += '<input type="hidden" name="memo" value="'+document.getElementById('group_memo').value+'" />';
		}
		
		function submitDrugGroup0(){
			var drugInGroup = document.getElementById('drug_in_group');
			var drugs = drugInGroup.childNodes;
			var formDrugs = document.getElementById('form_drugs');
			formDrugs.innerHTML = '';
			
			for (var i=0; i<drugs.length; ++i){
				var drug = drugs[i];
				if (drug.value)
					formDrugs.innerHTML += '<input type="hidden" name="drugs[]" value="'+drug.value+'" />';
			}
			
			submitDrugGroupMeta(formDrugs);
			document.getElementById('my_form').submit();
		}
		
		function submitDrugGroup1(){
			var formDrugs = document.getElementById('form_drugs');
			formDrugs.innerHTML = '';
			
			formDrugs.innerHTML += '<input type="hidden" name="column" value="'+document.getElementById('group_column').value+'" />';
			var conditions = (document.getElementById('group_condition').value).split(' ');
			for (var i=0; i<conditions.length; ++i){
				formDrugs.innerHTML += '<input type="hidden" name="conditions[]" value="'+conditions[i]+'" />';
			}
			
			submitDrugGroupMeta(formDrugs);
			document.getElementById('my_form').submit();
		}
		
		function submitDrugGroupRemove(){
			if (confirm('��ȷ��Ҫɾ����'))
				document.getElementById('my_form_remove').submit();
		}
	</script>
</html>