<?php
	header("Content-type: text/html; charset=gbk");
?>
<html>
	<head>
		<title>处方点评系统</title>
	</head>
	<body style="text-align:center">
		<h1>处方点评系统</h1>
		<ul>
			<li><h3><a href="./select.php">点评任务</a></h3></li>
			<li><h3><a href="./report.php">生成报告</a></h3></li>
			<li><h3><a href="./drugs.php">药品分组管理</a></h3></li>
			<li><h3><a href="./equals.php?table=disease">等效诊断管理</a></h3></li>
			<li><h3><a href="./equals.php?table=combine">联合用药管理</a></h3></li>
			<li><h3><a href="./dosage.php">剂量运算管理</a></h3></li>
		</ul>
	</body>
</html>