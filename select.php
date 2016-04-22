<?php
	include_once('core/util.php');
	Util::startTimer();

	include_once('core/database.php');
	global $DB;
	$DB = new Database();
	$DB->connect();
	
	include_once('models/test.php');
	include_once('models/drugs.php');
?>
<html>
	<head>
		<meta charset='gbk'>
		<style>
			
			#subsection_taskNum, #subsection_tasksId{
				display:none
			}
			h5{
				display:inline
			}
		</style>
	</head>
	<!--<link href="http://apps.bdimg.com/libs/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet">-->
	<body>
		<div >
			<form id='form' method='post' action='list.php'>
			
				<h3>��ѡ��</h3>
				
				<p id='section_mode'><h4>�û���</h4>
					<input type='radio' name='mode' value='0' id='mode_normal' checked /><label for='mode_normal'>��ͨҽ��</label>
					<input type='radio' name='mode' value='1' id='mode_chief' /><label for='mode_chief'>�鳤</label>
				</p>
				
				<p><h4>����ģʽ��</h4>
					<select name='task' id='task_'>
						<option value='-1' id='task_none' selected>������</option>
						<option value='-2' id='task_old' disabled='disabled'>������</option>
						<option value='0' id='task_assign' disabled='disabled'>����������</option>
						<?php
							$query = $DB->query('select distinct `User` from `tasks`');
							while ($row = mysql_fetch_array($query)){
								$id = $row['User'];
								echo "<option value='$id' >����$id</option>";
							}
						?>
					</select name='task'>
					<span id='subsection_tasksId'>
						<h5>�������ţ�</h5>
						<select name='tasksId' id='tasksId_' onchange="autofill()">
						<?php
							$settingsMeta = Test::loadAllTaskSettings();
							if ($settingsMeta['error'] == 0){
								$settings = $settingsMeta['data'];
								foreach ($settings as $setting){
									$id = $setting['id'];
									$time = $setting['time'];
									$v = $setting['value'];
									$parameters = array($v->dateFrom, $v->dateTo, $v->limit, $v->limit2, $v->limit3, $v->taskNum, $v->type);
									$parameters = implode(',', $parameters);
									$drugs = implode(',', $v->drugGroups);
									echo "<option value='$id' class='option_tasks'
										parameters=\"$parameters\");' drugs='$drugs'
										>������ $id ($time)</option>";
								}
							}
						?>	
						</select>
					</span>
					<span id='subsection_taskNum'>
						<h5>����������</h5>
						<input name='taskNum' type='text' value='3' id='taskNum_' />
					</span>
				</p>
				
				<div  id='section_columns'>
					<h4>�������棺</h4>
					<?php
						//����Ϊ�˶�ȡ������Ϣ
						foreach (Database::$COLUMNS_EVALS_NOCHECKED as $COLUMN => $NAME){
							echo "<input type='checkbox' name='columns[]' class='' value='$COLUMN' id='column_$COLUMN' checked /><label for='column_$COLUMN'>$NAME</label>";
						}
					?>
					<input type='hidden' name='columns[]' value='Checked'/>
				</div>
				
				<div >
					<h4>���ڣ�</h4>
					<div >
						<label>��</label><input type='date' class="form-control" id='dateFrom_' name='dateFrom' value='' />
						<label>��</label><input type='date' class="form-control" id='dateTo_' name='dateTo' value='' />
					</div>
				</div>
				
				<span id='sections_filters'>
					<div  id='section_type'>
						<h4>�������ͣ�</h4>
						<input type='radio' name='type' class="form-control" value='' id='type_all' checked /><label for='type_all'>ȫ��</label>
						<input type='radio' name='type' class="form-control" value='normal' id='type_normal' /><label for='type_normal'>����</label>
						<input type='radio' name='type' class="form-control" value='emergency' id='type_emergency' /><label for='type_emergency'>����</label>
						<input type='radio' name='type' class="form-control" value='hospitalized' id='type_hospitalized' /><label for='type_hospitalized'>סԺ</label>
					</div>
					
					<p id='section_limit'><h5>����ҽ��������</h5>
						<input type='text' name='limit' value='0' id='limit_' /><label for='limit_'>��ҽʦ��0Ϊȫ��ҽʦ��</label>
					</p>
					<p id='section_limit'><h5>ҽ�������������</h5>
						���<input type='text' name='limit2' value='0' id='limit2_' /><label for='limit2_'>��������0Ϊû�����ƣ�</label>
					</p>
					<p id='section_limit'><h5>����ҩƷ�������</h5>
						���<input type='text' name='limit3' value='0' id='limit3_' /><label for='limit3_'>��ҩƷ��0Ϊû�����ƣ�</label>
					</p>
					<input type='hidden' name='random' value='1'/>
					
					<div id='section_drugs'><h5>���˵�ҩƷ��ǩ��</h5>
						<ul>
						<?php
							$groups = Drugs::getGroups();
							foreach ($groups as $group){
								echo "<li><input type='checkbox' name='drugGroups[]' id='drug_{$group['notation']}' value='{$group['notation']}'/>{$group['name']}({$group['notation']})</li>";
							}
						?>
						</ul>
					</div>
				</span>
					
				<input type='submit' />
				<input type='button' onclick="location.href='./';" value="����"/>
			</form>
		</div>
	</body>
	<!--<script src="http://apps.bdimg.com/libs/bootstrap/3.3.0/js/bootstrap.min.js"></script>-->
	<script language='javascript' src="./js/select.js"></script>
</html>
<?php Util::endTimer();?>