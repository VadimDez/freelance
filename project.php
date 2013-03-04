<?php
	session_start();
	include('dbConnect.php');
	
	// variabili
	$projects = '';
	
	
	if(isset($_GET['id']))
	{
		// se passa id del progetto
		$idProj = $_GET['id'];
		
		$query = mysql_query("SELECT * FROM project,users WHERE idProj = '$idProj' AND project.idUser = users.idUser") or die(mysql_error());
		if(mysql_num_rows($query) == 0 )
		{
			$projects = "il prog. e' stato cancellato o non esiste";
		}
		else
		{
			$row = mysql_fetch_assoc($query);
			$nameOwner 		 = $row['name'];
			$secondnameOwner = $row['secondname'];
			$idOwner  		 = $row['idUser'];
			$imgOwner		 = $row['img'];
			$tipoOwner		 = $row['tipo'];
			if($tipoOwner == 0)
			{
				$tipoOwner = "Privato";
			}
			else
			{
				$tipoOwner = "Azienda";	
			}
			
			$prezzo			 = $row['prezzo'];
			$nameProj 		 = $row['nomeProj'];
			$descProj		 = $row['descrizione'];
			$richesteProj	 = $row['richieste'];
			
			// controllo se progetto non e' chiuso ==================================================================================
			// closed = 0 - aperto
			// closed = 1 - chiuso perche' il proprietario ha selto la persona
			// closed = 2 - chiuso perche' il proprietario ha deciso di chiudere
			if($row['closed'] >= "1")
			{
				$closed = true;	
			}
			else
			{
				$closed = false;	
			}
			
			// controllo per il tasto per candidarsi =================================================================================
			if(isset($_SESSION['idUser'])) // se ha gia fatto login
			{
				$idUser = $_SESSION['idUser'];
				// controllo se e' il proprietario
				$candidato = mysql_query("SELECT idUser FROM project WHERE idProj='$idProj' AND idUser='$idUser'") or die(mysql_error());
				if(mysql_num_rows($candidato) == 0)
				{
					$proprietario = false;
					// controllo se utente ha gia partecipato
					$candidato = mysql_query("SELECT idUser FROM candidati WHERE idProj='$idProj' AND idUser='$idUser'") or die(mysql_error());
					if(mysql_num_rows($candidato) == 0)
					{
						// non ha ancora partecipato - aggiungo bottone per candidarsi
						$joined = false;
					}
					else
					{
						// sta partecipando - aggiungo bottone "esci"
						$joined = true;
					}
				}
				else
				{
					$proprietario = true;
					
					// controllo se' utente puo' ritirare file del prog. e lasciare feedback
					if(mysql_num_rows(mysql_query("SELECT idUser FROM project WHERE idUser='$idUser' AND idProj='$idProj' AND ownerPaid='1' AND delivered='1' AND closed='1'")) == 1)
					{
						$finished = true;
					}
				}
			
			}
			
			$comments = '';
			$query = mysql_query("SELECT * FROM candidati, users WHERE idProj='$idProj' AND candidati.idUser = users.idUser") or die(mysql_error());
			if(mysql_num_rows($query) == 0)
			{
				// in caso se non ci sono proposte
				$comments .= '<div class="well"><p class="text-center">Non ci sono proposte</p></div>';	
			}
			else
			{
				
					if($proprietario == true)
					{
						// controllo se proprietario ha gia' sceglto il candidato
						$controllo = mysql_query("SELECT idUser FROM vincenti WHERE idProj='$idProj'") or die(mysql_error());
						if(mysql_num_rows($controllo) == 1)
						{
							// se si:
							$decide = true;
							$check = mysql_query("SELECT idUser FROM project WHERE idProj='$idProj' AND ownerPaid='1'") or die(mysql_error());
							if(mysql_num_rows($check) == 1)
							{
								$pagato = true;	
							}
						}
						else
						{
							// se no:
							$decide = false;
						}
					}
				
				// stampo i commenti , cioe' proposte
				while($row = mysql_fetch_assoc($query))
				{
					$comments .= '<div class="media">
						  <a class="pull-left" href="profile.php?id=' . $row['idUser'] .'">
							<img class="media-object" src="' . $row['img'] . '" width="64px">
						  </a>
						  <div class="media-body">
							<h4 class="media-heading">' . $row['name'] . ' ' . $row['secondname'] . '</h4>';
							if(!$decide && $proprietario)
							{
								//$comments .= '<input type="button" value="Scegli" id="take" data-in="' . $idProj . '"/>';
								$comments .= '<div id="choose">
												<a href="#scegli" role="button" class="btn" data-toggle="modal">Scegli</a>
												
												<div id="scegli" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												  <div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
													<h3 id="myModalLabel">Scelta del candidato</h3>
												  </div>
												  <div class="modal-body">
													<p>Sei sicuro di scegliere questo candidato per fare il tuo progetto?</p>
												  </div>
												  <div class="modal-footer">
													<button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button>
													<button class="btn btn-primary" data-dismiss="modal" aria-hidden="true" id="take" proj="' . $idProj . '" usr="' . $row['idUser'] . '">Scegli</button>
												  </div>
												</div></div>';
							}
							$comments .= $row['comment'] . '
							<div class="media">
							  <small>' . $row['data'] . '</small>
							</div>
						  </div>
						</div>';
				}
			}
			
		}
	}
	else
	{
		$query = mysql_query("SELECT * FROM project ORDER BY dataProj DESC") or die(mysql_error());
		if(mysql_num_rows($query) == 0 )
		{
			$projects = "non ci sono progetti da fare";
		}
		else
		{
			while($row = mysql_fetch_assoc($query))
			{
				// stampo la lista dei progetti
				$projects .= '<div><a href="project.php?id=' . $row['idProj'] . '">' . $row['nomeProj'] . '</a></div> <br/>';
			}
		}
	}
	mysql_close();
	if(!isset($_GET['id']))
	{
		header('location:index.php');
		exit();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php if($nameProj)
		{
			print $nameProj;
		}
		else
		{
			print "Progetti";	
		}?>
		</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
   		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link rel="stylesheet" type="text/css" href="style/main.css" >
</head>
	<body>
    	<?php
			include('template/header.html');
			include('template/project.html');
			include('template/footer.html');
		?>
        
        
        <script type="text/javascript" src="script/jquery-1.8.3.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="script/inProject.js"></script>
        <?php
		if($proprietario)
		{
			?>
			<script type="text/javascript" src="script/take.js"></script>	
		<?php
        }
        ?>
    </body>

</html>