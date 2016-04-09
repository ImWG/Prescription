<?php
	include_once('core/util.php');
	Util::startTimer();

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
?>
<html>
	<head>
		<meta charset='gbk'>
		<style>
			body, table{
				font-size:9px;
			}
			#subsection_taskNum{
				display:none
			}
		</style>
	</head>
	<body>
		<form id='form' method='post' action='test.php'>
			<h3>请选择</h3>
			
			<p id='section_mode'>用户：
				<input type='radio' name='mode' value='0' id='mode_normal' checked /><label for='mode_normal'>普通医生</label>
				<input type='radio' name='mode' value='1' id='mode_chief' /><label for='mode_chief'>组长</label>
			</p>
			
			<p>任务模式：
				<select name='task' id='task_'>
					<option value='-1' id='task_none' selected>无任务</option>
					<option value='0' id='task_assign' disabled='disabled'>分配任务</option>
					<?php
						$query = $DB->query('select distinct `User` from `tasks`');
						while ($row = mysql_fetch_array($query)){
							$id = $row['User'];
							echo "<option value='$id' >任务$id</option>";
						}
					?>
				</select name='task'>
				<span id='subsection_taskNum'>
					任务人数：
					<input name='taskNum' type='text' value='3' id='taskNum_' />
				</span>
			</p>
			
			<p id='section_columns'>点评方面：
				<?php
					//这里为了读取列名信息
					foreach (Database::$COLUMNS_EVALS_NOCHECKED as $COLUMN => $NAME){
						echo "<input type='checkbox' name='columns[]' value='$COLUMN' id='column_$COLUMN' checked /><label for='column_$COLUMN'>$NAME</label>";
					}
				?>
				<input type='hidden' name='columns[]' value='Checked'/>
			</p>
			
			<p>日期：
				从<input type='date' name='dateFrom' value='' />
				到<input type='date' name='dateTo' value='' />
			</p>
			
			<span id='sections_filters'>
				<p id='section_type'>处方类型：
					<input type='radio' name='type' value='' id='type_all' checked /><label for='type_all'>全部</label>
					<input type='radio' name='type' value='normal' id='type_normal' /><label for='type_normal'>门诊</label>
					<input type='radio' name='type' value='emergency' id='type_emergency' /><label for='type_emergency'>急诊</label>
					<input type='radio' name='type' value='hospitalized' id='type_hospitalized' /><label for='type_hospitalized'>住院</label>
				</p>
				
				<p id='section_limit'>点评医生名单：
					<input type='text' name='limit' value='0' id='limit_' /><label for='limit_'>名医师（0为全部医师）</label>
				</p>
				<p id='section_limit'>医生处方最大数：
					最多<input type='text' name='limit2' value='0' id='limit2_' /><label for='limit2_'>件处方（0为没有限制）</label>
				</p>
				<p id='section_limit'>处方药品最大数：
					最多<input type='text' name='limit3' value='0' id='limit3_' /><label for='limit3_'>件药品（0为没有限制）</label>
				</p>
				<input type='hidden' name='random' value='1'/>
			</span>
			
		
			<input type='submit' />
		</form>
	</body>
	<script language='javascript'>
		var task_ = document.getElementById('task_');
		task_.onchange = function(){
			if (task_.value > '0'){
				document.getElementById('sections_filters').style.display = 'none';
			}else{
				document.getElementById('sections_filters').style.display = 'block';
			}
			if (task_.value == '0'){
				document.getElementById('subsection_taskNum').style.display = 'inline';
			}else{
				document.getElementById('subsection_taskNum').style.display = 'none';
			}
		}
		
		document.getElementById('mode_normal').onclick = function(){
			document.getElementById('task_assign').disabled = 'disabled';
		}
		document.getElementById('mode_chief').onclick = function(){
			document.getElementById('task_assign').disabled = '';
		}
	</script>
</html>
<?php Util::endTimer();?>