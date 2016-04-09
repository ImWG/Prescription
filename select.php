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
			<h3>��ѡ��</h3>
			
			<p id='section_mode'>�û���
				<input type='radio' name='mode' value='0' id='mode_normal' checked /><label for='mode_normal'>��ͨҽ��</label>
				<input type='radio' name='mode' value='1' id='mode_chief' /><label for='mode_chief'>�鳤</label>
			</p>
			
			<p>����ģʽ��
				<select name='task' id='task_'>
					<option value='-1' id='task_none' selected>������</option>
					<option value='0' id='task_assign' disabled='disabled'>��������</option>
					<?php
						$query = $DB->query('select distinct `User` from `tasks`');
						while ($row = mysql_fetch_array($query)){
							$id = $row['User'];
							echo "<option value='$id' >����$id</option>";
						}
					?>
				</select name='task'>
				<span id='subsection_taskNum'>
					����������
					<input name='taskNum' type='text' value='3' id='taskNum_' />
				</span>
			</p>
			
			<p id='section_columns'>�������棺
				<?php
					//����Ϊ�˶�ȡ������Ϣ
					foreach (Database::$COLUMNS_EVALS_NOCHECKED as $COLUMN => $NAME){
						echo "<input type='checkbox' name='columns[]' value='$COLUMN' id='column_$COLUMN' checked /><label for='column_$COLUMN'>$NAME</label>";
					}
				?>
				<input type='hidden' name='columns[]' value='Checked'/>
			</p>
			
			<p>���ڣ�
				��<input type='date' name='dateFrom' value='' />
				��<input type='date' name='dateTo' value='' />
			</p>
			
			<span id='sections_filters'>
				<p id='section_type'>�������ͣ�
					<input type='radio' name='type' value='' id='type_all' checked /><label for='type_all'>ȫ��</label>
					<input type='radio' name='type' value='normal' id='type_normal' /><label for='type_normal'>����</label>
					<input type='radio' name='type' value='emergency' id='type_emergency' /><label for='type_emergency'>����</label>
					<input type='radio' name='type' value='hospitalized' id='type_hospitalized' /><label for='type_hospitalized'>סԺ</label>
				</p>
				
				<p id='section_limit'>����ҽ��������
					<input type='text' name='limit' value='0' id='limit_' /><label for='limit_'>��ҽʦ��0Ϊȫ��ҽʦ��</label>
				</p>
				<p id='section_limit'>ҽ�������������
					���<input type='text' name='limit2' value='0' id='limit2_' /><label for='limit2_'>��������0Ϊû�����ƣ�</label>
				</p>
				<p id='section_limit'>����ҩƷ�������
					���<input type='text' name='limit3' value='0' id='limit3_' /><label for='limit3_'>��ҩƷ��0Ϊû�����ƣ�</label>
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