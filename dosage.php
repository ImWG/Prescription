<?php
	$table = $_GET['table'];
?>
<html>
	<head>
		<meta charset='gbk'>
		<title>剂量管理</title>
	</head>
	<body>
		<p>上传XML文件并导入</p>
		<form id='xml' action='p_xmlequals.php?table=<?php echo $table; ?>&action=load' method='post' enctype='multipart/form-data'>
			<input type='file' name='xmlFile' />
			<input type='submit' />
		</form>
		<p>导出数据为XML文件</p>
		<input type='button' value='导出' onclick='location.href="p_xmlequals.php?table=<?php echo $table; ?>&action=export"'/>
	</body>
</html>