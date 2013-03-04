<?php
if(!include('check.php'))
{
	header('Location:login.php');
	exit;
}
else
{
	// variabili
	$myID = $_SESSION['idUser'];
	
	$projectName ='';
	$tipo ='';
	$categoria ='';
	$prezzo ='';
	$descrizione ='';
	$richieste ='';
	$categorie = '';
	include('dbConnect.php');
	$query = mysql_query("SELECT * FROM categorie") or die(mysql_error());
	while($row = mysql_fetch_assoc($query))
	{
			$categorie .= '<label class="radio"><input type="radio" value="' . $row['idCat'] . '" name="c">' . $row['nomeCat'] . '</label>';
	}
	
	
	if(isset($_POST['done']))
	{
		if($_POST['projName'] == '')
		{
				$error .= '<p class="text-error">Scrivi il nome del progetto</p>';
		}
		
		/*if(!isset($_POST['tipoProj']))
		{
			$error .= '<p class="text-error">Scegli il tipo del progetto.</p>';
		}*/
		
		if(!isset($_POST['c']))
		{
			$error .= '<p class="text-error">Scegli la categoria del progetto.</p>';
		}
		
		if(!isset($_POST['prezzo']))
		{
			$error .= '<p class="text-error">Scrivi il prezzo del progetto.</p>';
		}
		
		
		if(!$error)
		{
			$projectName = $_POST['projName'];
			$tipoProj	 = $_POST['tipoProj'];
			$categoria	 = $_POST['c'];
			$prezzo		 = $_POST['prezzo'];
			$descrizione = $_POST['d'];
			// pars
			
			$richieste	 = $_POST['r'];
			// pars
			
			// salvo in database
			
			
			$query = mysql_query("INSERT INTO project(idProj,idUser,nomeProj,tipoProj,idCategoria,prezzo,descrizione,richieste,dataProj,categoria) VALUES(NULL,'$myID', '$projectName', '$tipoProj', '$categoria', '$prezzo', '$descrizione', '$richieste', now(), '$categoria')") or die(mysql_error());
			// se e' stato aggiunto faccio redirect al progetto
			if($query)
			{
				$query = mysql_query("SELECT idProj	FROM project WHERE idUser='$myID' ORDER BY dataProj DESC LIMIT 1") or die(mysql_error());
				$query = mysql_fetch_assoc($query);
				header('location:project.php?id=' . $query['idProj']);
				exit();
			}
		}
		
		
		// per levare il problema di aggiornamento
		/*echo '<script type="text/javascript">';
    	echo 'window.location.reload();';
    	echo '</script>';*/
	}
	
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    	<title>Nuovo progetto</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Bootstrap -->
   		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" type="text/css" href="style/main.css" >
    </head>
    <body>
    	<?php
			include('template/header.html');
			include('template/newproj.html');
			include('template/footer.html');
		?>
        <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>