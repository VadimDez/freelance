<?php
	session_start();
	if(include('../check.php'))
	{
		if(isset($_POST['idProj']) && $_POST['idProj'] != "" && isset($_POST['type']))
		{
			// db connect
			include('../model.php');
			$connect = new myConnection;
			$connect->connect();
			$idUser = $_SESSION['idUser'];
			$text 	= $_POST['text'];

            $parser = new parser();

            $text = $parser->textParsing($text);

			$idProj = $_POST['idProj'];
			if($_POST['type'] == "join" && isset($_POST['text']))
			{
				mysql_query("INSERT INTO candidati(idProj, idUser, data, comment) VALUES('$idProj', '$idUser', now(), '$text')") or die(mysql_error());
			}
			else
			{
				if($_POST['type'] == "left")
				{
					mysql_query("DELETE FROM candidati WHERE idUser='$idUser' AND idProj='$idProj'") or die(mysql_error());
					
					if(mysql_num_rows(mysql_query("SELECT * FROM vincenti WHERE idUser='$idUser' AND idProj='$idProj'")) > 0)
					{
						mysql_query("DELETE FROM vincenti WHERE idUser='$idUser' AND idProj='$idProj'");
					}
				}
				else
				{
					if($_POST['type'] == "close")
					{
						mysql_query("UPDATE project SET closed='2' WHERE idProj='$idProj'") or die(mysql_error());
					}
				}
			}
			
			// chiudo connessione
			$connect->close();
			//echo json_encode($risultato);	
		}	
	}
	else
	{
		header('../registration.php');	
	}
	
?>