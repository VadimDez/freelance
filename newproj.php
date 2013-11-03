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

        if(!isset($_POST['tipo']))
        {
            $error .= '<p class="text-error">Scegli il tipo del progetto.</p>';
        }
		
		/*if(!isset($_POST['tipoProj']))
		{
			$error .= '<p class="text-error">Scegli il tipo del progetto.</p>';
		}*/
		
		if(!isset($_POST['c']))
		{
			$error .= '<p class="text-error">Scegli la categoria del progetto.</p>';
		}
		/*
		   if((!isset($_POST['prezzo']) || (($_POST['prezzo'] < 1) && ($_POST['optionsRadios'] != 'prezzo3'))) && ($_POST['tipo'] != '3') || ($_POST['optionsRadios'] == "prezzo2" && ($_POST['prezzo'] < 0 || ($_POST['prezzo2'] < 0 || $_POST['prezzo2'] < $_POST['prezzo']))))
            {
                $error .= '<p class="text-error">Inserisci il prezzo del progetto.</p>';
            }
		 */

		if(!isset($_POST['prezzo']))
        {
			$error .= '<p class="text-error">Inserisci il prezzo del progetto.</p>';
		}

        if($_POST['prezzo'] == '' && $_POST['optionsRadios'] == 'prezzo1')
        {
            $error .= '<p class="text-error">Inserisci il prezzo del progetto.</p>';
        }

        if(((($_POST['prezzo'] < 1) && ($_POST['optionsRadios'] == 'prezzo3'))) && ($_POST['tipo'] == '3'))
        {
            $error .= '<p class="text-error">Inserisci il prezzo del progetto.</p>';
        }
        if(($_POST['optionsRadios'] == "prezzo2" && ($_POST['prezzo'] < 0 || ($_POST['prezzo2'] < 0 || $_POST['prezzo2'] < $_POST['prezzo']))))
        {
            $error .= '<p class="text-error">Inserisci il prezzo del progetto.</p>';
        }

        if($_POST['optionsRadios'] != 'prezzo2' && $_POST['prezzo1'] > $_POST['prezzo2'])
        {
            $error .= '<p class="text-error">Inserisci correttamente il prezzo.</p>';
        }

        if(!isset($_POST['d']) || (strlen($_POST['d']) < 1))
        {
            $error .= '<p class="text-error">Scrivi la descrizione del progetto.</p>';
        }
		
		
		if(!$error)
		{
            // includo
            include('model.php');
            $parser = new parser();

			$projectName = $_POST['projName'];
            //pars
            $projectName = $parser->textParsing($projectName);

			$tipoProj	 = $_POST['tipo']; // tipo 1 = cerco freelancer, tipo 2 = cerco sponsor, tipo 3 = Cerco collaboratore
			$categoria	 = $_POST['c'];

            // prezzo
            $prezzo		 = $_POST['prezzo'];
            // controllo la scelta della tipologia del prezzo
            // se 1 - quindi c'e' un solo prezzo
            if($_POST['optionsRadios'] == 'prezzo1')
            {
                $prezzo2 = NULL;
            }
            else
            {
                if($_POST['optionsRadios'] == 'prezzo2')
                {
                    $prezzo	 = $_POST['prezzo1'];
                    $prezzo2 = $_POST['prezzo2'];
                }
                else
                {
                    $prezzo = NULL;
                    $prezzo2= NULL;
                }
            }




			$descrizione = $_POST['d'];
            // pars
			$descrizione = $parser->textParsing($descrizione);

			$richieste	 = $_POST['r'];
			// pars
            $richieste   = $parser->textParsing($richieste);
			
			// salvo in database
			
			
			$query = mysql_query("INSERT INTO project(idProj,idUser,nomeProj,tipoProj,idCategoria,prezzo,prezzo2,descrizione,richieste,dataProj,categoria) VALUES(NULL,'$myID', '$projectName', '$tipoProj', '$categoria', '$prezzo', '$prezzo2', '$descrizione', '$richieste', now(), '$categoria')") or die(mysql_error());
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

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    	<title>Nuovo progetto</title>
        <?php include('include.php'); ?>
    </head>
    <body>
    	<?php
			include('template/header.html');
			include('template/newproj.html');
			include('template/footer.html');
		?>

    </body>
</html>