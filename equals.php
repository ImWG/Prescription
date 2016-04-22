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
			
				// ************************* 分组列表
			
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
				
				// ************************* 创建/修改动态分组
		?>
				
				<div class="box" id="box_right">

				</div>
				
				<div class="box" id="box_additional">
					<p>编号：<?php echo $group['id']; ?></p>
					<p>代号：<input type='text' id='group_notation' value='<?php echo $group['notation']; ?>'/></p>
					<p>名称：<input type='text' id='group_name' value='<?php echo $group['name']; ?>'/></p>
					<?php
						//动态标签的填写部分
						
							$tData = explode("\0", $group['data']);
							$group['condition'] = implode(' ', $tData);
							
							echo '<p>----------------------------</p>';
							
							echo "<p>别名：<textarea id='group_condition'>{$group['condition']}</textarea></p>";
							echo "<p>不同别名之间用空格分开，关键字前后加“%”则表示前后可有其他文字。</p>";
						
					?>
					<p>备注：<textarea id='group_memo'><?php echo $group['memo']; ?></textarea></p>
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
				<input type='button' id='button_modify' value='修改' onclick='submitDrugGroup1();'/>
			<?php if( $function == 'modify' ){ ?>
				<input type='button' id='button_remove' value='删除' onclick='submitDrugGroupRemove();'/>
				<input type='button' id='button_resume' value='返回' onclick='location.href="equals.php";'/>
			<?php } else {?>
				<input type='button' id='button_resume' value='返回' onclick='location.href=".";'/>
			<?php } ?>
				<iframe id='frame_response' name='frame_response' sandbox=''></iframe>
		
	</body>
	<script src="./js/drugs.js"></script>
</html>