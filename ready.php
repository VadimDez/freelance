<?php
	if((include('check.php'))  && isset($_GET['id']) && $_GET['id'] != "")
	{
		$idUser = $_SESSION['idUser'];
		$idProj = $_GET['id'];
		include('model.php');
		$conn = new myConnection;
		$conn->connect();
		// controllo se utente e' proprietario del progetto
		$query = mysql_query("SELECT * FROM project,categorie WHERE project.idProj='$idProj' AND project.idUser='$idUser' AND project.ownerPaid='1' AND project.delivered='1' AND categorie.idCat=project.categoria") or die(mysql_error());
		if(mysql_num_rows($query) == 1)
		{
			$projInfo = mysql_fetch_assoc($query);
			// info sul progetto
			$projName	= $projInfo['nomeProj'];
			$projPrezzo	= $projInfo['prezzo'];
			$projCat	= $projInfo['nomeCat'];
			
			
			// info su utente che ha fatto il progetto
			$vincenteInfo = mysql_query("SELECT * FROM vincenti,users WHERE vincenti.idProj='$idProj' AND users.idUser=vincenti.idUser") or die(mysql_error());
			$vincenteInfo = mysql_fetch_assoc($vincenteInfo);
			$winID			= $vincenteInfo['idUser'];
			$winImg			= $vincenteInfo['img'];
			$winName		= $vincenteInfo['name'];
			$winSecondname	= $vincenteInfo['secondname'];
			
			$feedback = new ready;
			$existFeedBack = $feedback->existFeedBack($idProj);
			
			include('template/ready.html');
		}
		else
		{
			// in caso che ci sia un errore
			$conn->close();
			header('location:index.php');
			exit();	
		}
		$conn->close();
	}
	else
	{
		header('location:index.php');
		exit();
	}
?>