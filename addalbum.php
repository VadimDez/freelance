<?php

if(include('check.php'))
{
	// prende le info di utente
	include('pick.php');
	
	if(isset($_POST['add']))
	{
		$nameAlbum = $_POST['nameAlbum'];
		if(strlen($nameAlbum) > 0)
        {
            $idUser        = $_SESSION['idUser'];

            // parsing
            $nameAlbum = stripslashes($nameAlbum);
            $nameAlbum = strip_tags($nameAlbum);
            $nameAlbum = mysql_real_escape_string($nameAlbum);
            $nameAlbum = trim($nameAlbum, '\r\n');
            $nameAlbum = preg_replace('/\'/i','&#39', $nameAlbum);
            $nameAlbum = preg_replace('/`/i','', $nameAlbum);
            $nameAlbum = mysql_real_escape_string($nameAlbum);
            // fine parsing

            mysql_query("INSERT INTO albums(idAlbum, idUser, nameAlbum, dataAlbum) VALUES(NULL, '$idUser', '$nameAlbum', now())") or die(mysql_error());
            $query = mysql_query("SELECT idAlbum FROM albums WHERE idUser='$idUser' ORDER BY dataAlbum DESC LIMIT 1") or die(mysql_error());
            $query = mysql_fetch_assoc($query);
            header('location:albums.php?album=' . $query['idAlbum']);
            mysql_close();
            exit();
        }
        else
        {
            $error = '<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a><span>Inserisci correttamente nome dell\'album</span><br/></div>';
        }
	}
}
else
{
	header('location:albums.php');
	exit();
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
    <?php include('include.php'); ?>
</head>

<body>
<?php
	include('template/header.html');
	include('template/addalbum.html');
    include('template/footer.html');
?>
</body>
</html>