<?php
	/*
		视图名：equals
		要实现的功能：等效诊断名的显示、修改、创建、删除功能
		参数：
			修改等效诊断名的代号 - notation
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
			//挑选评价类
			if ($table == 'disease'){
				$currentEquals = new DiseaseEquals();
			}else if ($table == 'combine'){
				$currentEquals = new CombineEquals();
			}
		
			// ************************* 分组列表
		
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
			
			// ************************* 创建/修改
		?>
				
			<div class="box" id="box_right">
				<span id='t_list'></span>
			</div>
			
			<div class="box" id="box_additional">
			<?php 
				if( $table == 'disease' ){
					$labels = array('id'=>'编号','notation'=>'代号', 'name'=>'名称', 'condition'=>'别名', 'column'=>'对症标签', 'memo'=>'备注');
				}elseif ($table == 'combine') {
					$labels = array('id'=>'编号','notation'=>'代号', 'name'=>'名称', 'condition'=>'冲突药品', 'column'=>'', 'memo'=>'备注');
				}
			?>
				<p><?php echo $labels['id'];?>：<?php echo $group['id']; ?></p>
				<p><?php echo $labels['notation'];?>：<input type='text' id='group_notation' value='<?php echo $group['notation']; ?>'/></p>
				<p><?php echo $labels['name'];?>：<input type='text' id='group_name' value='<?php echo $group['name']; ?>'/></p>
				<?php
					//填写部分
					$tData = explode("\0", $group['data']);
					$group['condition'] = implode(' ', $tData);
					
					echo '<p>----------------------------</p>';
					
					echo "<p>{$labels['condition']}：<textarea id='group_condition'>{$group['condition']}</textarea></p>";
					echo "<p>不同{$labels['condition']}之间用空格分开。</p>";
					
					if( $table == 'disease' ){
				?>
				<p><?php echo $labels['column'];?>：<input id='group_column' value='<?php echo $group['drugs']; ?>' />
				<?php }?>
				
				<p><?php echo $labels['memo'];?>：<textarea id='group_memo'><?php echo $group['memo']; ?></textarea></p>
			</div>
			
			<form method="post" action="p_equals.php?table=<?php echo $table;?>&type=modify" id='my_form' target='frame_response'>
				<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
				<input type='hidden' name='type' value='<?php echo $type; ?>'/>
				<span id='form_drugs'></span>
			</form>
			<form method="post" action="p_equals.php?table=<?php echo $table;?>&type=remove" id='my_form_remove' target='frame_response'>
				<input type='hidden' name='id' value='<?php echo $group['id']; ?>'/>
			</form>
			<input type='button' id='button_modify' value='修改' onclick='submitDrugGroup1();'/>
			
		<?php if( $function == 'modify' ){ ?>
			<input type='button' id='button_remove' value='删除' onclick='submitDrugGroupRemove();'/>
			<input type='button' id='button_resume' value='返回' onclick='location.href="equals.php?table=<?php echo $table;?>";'/>
		<?php } else {?>
			<input type='button' id='button_resume' value='返回' onclick='location.href=".";'/>
		<?php } ?>
		
			<iframe id='frame_response' name='frame_response' sandbox=''></iframe>
		<?php if ($table == 'disease') {?>
			<input type='button' id='button_other1' value='列出药品分组' onclick='loadDrugGroups();'/>
		<?php }elseif ($table == 'combine') { ?>
			<div id='button_other1'>
				<input type='button' value='列出药品' onclick='loadDrugsForEquals();'/>
				<input type='button' value='列出已选药品' onclick='loadDrugsForEqualsByIds("group_condition");'/>
			</div>
		<?php } ?>
		
	</body>
	<script src="./js/drugs.js"></script>
	<script src="http://apps.bdimg.com/libs/jquery/2.0.0/jquery.min.js"></script>
</html>