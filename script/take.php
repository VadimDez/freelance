<?php

	session_start();
	if(include('../check.php'))
	{
		if(isset($_POST['idProj']) && isset($_POST['idUser']))
		{
			$idProj 	= $_POST['idProj'];
			$idSelected = $_POST['idUser'];
			include('../model.php');
			$connect = new myConnection;
			$connect->connect();
			
			$query = mysql_query("SELECT * FROM candidati WHERE idUser='$idSelected' AND idProj='$idProj'") or die(mysql_error());
				
			if(mysql_num_rows($query) == 1)
			{
				mysql_query("INSERT INTO vincenti(idUser, idProj) VALUES('$idSelected','$idProj')") or die(mysql_error());
				mysql_query("UPDATE project SET closed='1' WHERE idProj='$idProj'") or die(mysql_error());
			}
			
			$connect->close();
		}
	}
	else
	{
		header('login.php');
	}

?>