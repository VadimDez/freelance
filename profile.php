<?php
	include('check.php');
    include('dbConnect.php');
	if(isset($_GET['id']))
	{
		$idUser = $_GET['id'];
        include_once('model.php');
        $parser = new parser();
        $idUser = $parser->textParsing($idUser);
	}
	else
	{
    	$idUser = $_SESSION['idUser'];
	}
    $query = mysql_query("SELECT * FROM users WHERE users.idUser = '$idUser'");
    if(mysql_num_rows($query) == 1)
    {
        $row = mysql_fetch_assoc($query);

        // info =============================================================
        $name      		= $row['name'];
        $secondname 	= $row['secondname'];
        $photo			= $row['img'];
        $tipo			= $row['tipo'];
        $city			= $row['city'];
        $dataReg		= $row['dataRegistrazione'];
        $day			= $row['day'];
        $month			= $row['month'];
        $year			= $row['year'];
        $info			= $row['about'];

        // data di registrazione
        $dataReg           = strftime("%d %b %Y", strtotime($dataReg));

        // feed
        $feedPos		= $row['feedPos'];
        $feedNeg		= $row['feedNeg'];

        // numero progetti partecipanti
        $numero			= mysql_query("SELECT * FROM candidati WHERE idUser='$idUser'") or die(mysql_error());
        $numProjPart	= mysql_num_rows($numero);

        // numero progetti vinti
        $numero			= mysql_query("SELECT * FROM vincenti WHERE idUser='$idUser' AND confirmed='1'") or die(mysql_error());
        $numProjWin		= mysql_num_rows($numero);

        // numero progetti da confermare per farlo
        $numero			= mysql_query("SELECT project.idProj FROM vincenti,project WHERE vincenti.idUser='$idUser' AND vincenti.idProj=project.idProj AND project.delivered='0'") or die(mysql_error());
        $numProjDaCons	= mysql_num_rows($numero);

        // numero progetti pubblicati
        $numero			= mysql_query("SELECT idProj FROM project WHERE idUser='$idUser'") or die(mysql_error());
        $numProjPubb	= mysql_num_rows($numero);

        // numero progetti pubblicati ancora aperti
        $numero			= mysql_query("SELECT idProj FROM project WHERE idUser='$idUser' AND closed = '0'") or die(mysql_error());
        $numProjPubbOpen= mysql_num_rows($numero);
        // ==================================================================
    }
    else
    {
        $notExist = true;
    }

	
	mysql_close();
?>

<!DOCTYPE HTML>
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Profilo di <?php print "$name $secondname";?> - ifreelancer.it</title>
        <?php include('include.php'); ?>
	</head>
<body>
    <?php
    	include('template/header.html');
        if(!$notExist)
        {
            include('template/profile.html');
        }
        else
        {
            print '<div class="show-of-head text-center span12"><br/>Utente non esiste oppure é stato cancellato.</div>';
        }

		include('template/footer.html');
	?>
</body>
</html>