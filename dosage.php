<?php
	$table = $_GET['table'];
?>
<html>
	<head>
		<meta charset='gbk'>
		<title>��������</title>
	</head>
	<body>
		<p>�ϴ�XML�ļ�������</p>
		<form id='xml' action='p_xmlequals.php?table=<?php echo $table; ?>&action=load' method='post' enctype='multipart/form-data'>
			<input type='file' name='xmlFile' />
			<input type='submit' />
		</form>
		<p>��������ΪXML�ļ�</p>
		<input type='button' value='����' onclick='location.href="p_xmlequals.php?table=<?php echo $table; ?>&action=export"'/>
	</body>
</html>