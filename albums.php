<?php
include('check.php'); 
// prende le info di utente
include('pick.php');

$albums    ='';
$addalbum  ='';
$owner     = false;
$photo     ='';
$msg       ='';
$back      ='';
$sender    = $_SESSION['idUser'];

if(isset($_GET['id']))
{
    // per vedere tutti album
    $userID = $_GET['id'];
    // controllo se sono album di utente
    if($userID == $sender)
    {
        $owner = true;
        $addalbum = '<a href="addalbum.php">+Aggiungi album</a>';
    }
    
    $borders = '1';
    $query = mysql_query("SELECT * FROM albums WHERE idUser = '$userID'") or die(mysql_error());
    $albums = '<div>';
	if(mysql_num_rows($query) > 0)
	{
		while($row = mysql_fetch_assoc($query))
		{
			//$albums .= '<div style="padding:10px;" class="cover"><a href="albums.php?album=' . $row['idAlbum'] . '"><img src="' . $row['cover'] . '" class="img-polaroid"><div id="title">' . $row['nameAlbum'] . '</div></a></div><div id="leftClear">&nbsp;</div>';
			$albums .= '<div class="row-fluid">
    <div class="span3">
      <a href="albums.php?album=' . $row['idAlbum'] . '"><img src="' . $row['cover'] . '" class="img-polaroid"></a>
    </div>
    <div class="span9">
      <a href="albums.php?album=' . $row['idAlbum'] . '">' . $row['nameAlbum'] . '</a>
    </div>
  </div>';
		}
	}
    else
    {
        // non ci sono album
        $albums .= '<center><span> 0 album </span></center>';
    }
    
    $albums .= '</div>';
}
else
{
    if(isset($_GET['album']))
    {
        // vedere un certo album
        $albumID = $_GET['album'];
        $query = mysql_query("SELECT * FROM albums WHERE idAlbum = '$albumID'") or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        if($row['idUser'] == $sender)
        {
            $owner = true;
            $addalbum = '<form method="post" action="albums.php"><input type="hidden" value="' . $albumID . '" name="addFoto" /><input type="submit" value="+Aggiungi foto"></form>';
        }
        // stampo le foto ===========================================================================================================================
        $query = mysql_query("SELECT * FROM photos WHERE idAlbum='$albumID'") or die(mysql_error());
        $photo = '<div>';
        $counter = 0;
        if(mysql_num_rows($query) > 0)
        {
            // ci sono le foto
            while($row = mysql_fetch_assoc($query))
            {
                $photo .= '<a href="photo.php?photo=' . $row['idPhoto'] . '"><div id="photo"><img src="' . $row['photo'] . '" class="img-polaroid" width="200px" /></div></a>';
                
                // controllo per stampare solo 12 foto a riga =========================================================
                $counter++;
                if($counter == 12)
                {
                    $photo .= '</div><div id="leftClear"></div><div>';
                    $counter = 0;
                }
                // ====================================================================================================
            }
        }
        else
        {
            // non ci sono le foto
            $photo .= '<center><span> 0 foto </span></center>';
        }
        $photo .= '</div>';
        // ===============================================================================================================================================
    }
    else
    {
        if(isset($_POST['addFoto']))
        {
            $albumID = $_POST['addFoto'];
            
            $back = '<a href="albums.php?album=' . $albumID . '">Torna all\'album</a>';
            
            $photo = '<div align="center">
	                <form action="albums.php" enctype="multipart/form-data" method="post" id="pic" name="img">
                    <b>Caricamento della foto</b>
	                <div>
					<input type="file" name="imgField" id="imgField"  class="button"/><br/>
                    Descrizione:
                    <input type="text" name="descr" maxlenght="1000" /><br/>
                    <input type="hidden" name="up" value="' . $albumID . '" />
                    
                    <input type="submit" value="Carica" name="imgButton" id="imgButton" class="button" />
                    </div>
	                </form>
                </div>';
        }
        else
        {
            if(isset($_POST['up']))
            {
                $albumID = $_POST['up'];
                $descr = $_POST['descr'];
                
                // parsing
                $descr = stripslashes($descr);
                $descr = strip_tags($descr);
                $descr = mysql_real_escape_string($descr);
                $descr = trim($descr, '\r\n');
                $descr = preg_replace('/\'/i','&#39', $descr);
                $descr = preg_replace('/`/i','', $descr);
                $descr = mysql_real_escape_string($descr);
                // fine parsing
                
                
                if(!$_FILES['imgField']['tmp_name'])
                {
                    $msg = '<font color="red">Prima devi selezionare l\'immagine.</font>';
                }
                else
                {
                    $maxsize = 10000000; // Utente puo' scegliere al massimo l'immagine che e' minore di 10MB
                    if($_FILES['imgField']['size'] > $maxsize)
                    {
                        $msg = '<font color=\"red\">Deve scegliere l\'immagine che ha grandezza minore di 10MB.</font>';
                        unlink($_FILES['imgField']['tmp_name']);
                    }
                    else
                    {
                        if(!preg_match("/\.(gif|jpg|png|jpeg)$/i",$_FILES['imgField']['name']))
                        {
                            $msg = "<font color=\"red\">Deve scegliere l'immagine di tipo GIF,JPG o PNG.</font>";
                            unlink($_FILES['imgField']['tmp_name']);
                        }
                        else
                        {
                            $query = mysql_query("SELECT MAX(idPhoto) AS num FROM photos") or die(mysql_error());
                            $row = mysql_fetch_assoc($query);
                            $var     = $row['num'];
                            $var     = $var + 1;
                            
                            $newImg = "photos/$var.jpg";
                            $newfile = move_uploaded_file($_FILES['imgField']['tmp_name'],$newImg);
                            
                            $msg = '<font color="green">L\'immagine e\' caricata.</font>';
                            
                            // query per caricare indirizzo dell'immagine nella database
                            if($query = mysql_query("INSERT INTO photos (photo, idAlbum, descr, idPhoto, dataPhoto) VALUES('$newImg', '$albumID',  '$descr', NULL, now())") or die(mysql_error()))
                            {
                                // cambio la cover, metto come la cover la foto appena caricata ====================================
                                mysql_query("UPDATE `albums` SET `cover`='$newImg' WHERE idAlbum='$albumID'") or die(mysql_error());
                                // =================================================================================================
                                header('location:albums.php?album=' . $albumID);
                                exit;
                            }
                        }
                    }
                }
            }
            else
            {
                header('location:albums.php?id=' . $sender);
                exit();
            }
        }
    }
}




mysql_close();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Untitled Document</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="style/main.css" >
    <link rel="stylesheet" type="text/css" href="style/albums.css" >
</head>

<body>
	<?php
        if(!$borders)
        {
            include('template/header.html');
        }
        include('template/albums.html');
        if(!$borders)
        {
            include('template/footer.html');
        }
    ?>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>
