<?php
	if(isset($_POST['idProj']) && isset($_POST['feedValue']) && isset($_POST['feedText']))
	{
		$idProj 	= $_POST['idProj'];
		$feedValue	= $_POST['feedValue'];
		$feedText	= $_POST['feedText'];
		
		include('../model.php');
		$feed = new ready;
		$conn = new myConnection;
		$conn->connect();
		
		$feed->insertFeed($idProj, $feedValue, $feedText);
		
		$conn->close();
	}
?>