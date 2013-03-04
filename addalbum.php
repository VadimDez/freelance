<?php

if(include('check.php'))
{
	// prende le info di utente
	include('pick.php');
	
	if(isset($_POST['add']))
	{
		$nameAlbum = $_POST['nameAlbum'];
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
		
		mysql_query("INSERT INTO albums(idAlbum, idUser, nameAlbum) VALUES(NULL, '$idUser', '$nameAlbum')") or die(mysql_error());
		$query = mysql_query("SELECT idAlbum FROM albums WHERE idUser='$idUser' ORDER BY dataAlbum DESC LIMIT 1") or die(mysql_error());
		$query = mysql_fetch_assoc($query);
		mysql_close();
		header('location:albums.php?id=' . $query['idAlbum']);
		exit();
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
<link rel="stylesheet" type="text/css" href="style/main.css" >
</head>

<body>
<?php
	include('template/header.html');
	include('template/addalbum.html');
    include('template/footer.html');
?>
</body>
</html>