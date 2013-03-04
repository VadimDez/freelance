<?php
	include('check.php');
    include('dbConnect.php');
	if(isset($_GET['id']))
	{
		$idUser = $_GET['id'];
	}
	else
	{
    	$idUser = $_SESSION['idUser'];
	}
    $query = mysql_query("SELECT * FROM users WHERE users.idUser = '$idUser'");
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
	$numero			= mysql_query("SELECT * FROM vincenti,project WHERE vincenti.idUser='$idUser' AND vincenti.idProj=project.idProj AND project.delivered='0'") or die(mysql_error());
	$numProjDaCons	= mysql_num_rows($numero);
	
	// numero progetti pubblicati
	$numero			= mysql_query("SELECT * FROM project WHERE idUser='$idUser'") or die(mysql_error());
	$numProjPubb	= mysql_num_rows($numero);
	
	// numero progetti pubblicati ancora aperti
	$numero			= mysql_query("SELECT * FROM project WHERE idUser='$idUser' AND closed='0'") or die(mysql_error());
	$numProjPubbOpen= mysql_num_rows($numero);
    // ==================================================================
	
	mysql_close();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Profilo di <?php print "$name $secondname";?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" type="text/css" href="style/main.css" >
	</head>
<body>
    <?php
    	include('template/header.html');
		include('template/profile.html');
		include('template/footer.html');
	?>
    <script type="text/javascript" src="script/jquery-1.8.3.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>