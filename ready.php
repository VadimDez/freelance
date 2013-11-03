<?php
	if((include('check.php'))  && isset($_GET['id']) && $_GET['id'] != "")
	{
		$idProj = $_GET['id'];
		include('model.php');
		$ready = new ready();
        $ready->readyStart($idProj);
	}
	else
	{
		header('location:index.php');
		exit();
	}
?>