<?php
	include_once('dbConnect.php'); // definizioni della database;
	include_once('model.php');
	//echo htmlspecialchars($_SESSION['username']);
	if(isset($_GET['id']))
	{
        $parser = new parser();
		$idUser = $parser->textParsing($_GET['id']);
		if(mysql_num_rows(mysql_query("SELECT idUser FROM users WHERE idUser='$idUser'")) == 0)
		{
			header('location:index.php');
			exit;
		}
	}
	else
	{
		$idUser = $_SESSION['idUser'];
	}
	
	// query per ottenere tutte le informazioni del utente
	$result = mysql_query("SELECT * FROM users WHERE idUser='$idUser'") or die(mysql_error());
	$row = mysql_fetch_assoc($result);
	$name       = $row['name'];
	$secondname = $row['secondname'];
	$photo      = $row['img'];
	$city       = $row['city'];
	$day		= $row['day'];
	$month		= $row['month'];
	$year		= $row['year'];
    $about		= $row['about'];
?>