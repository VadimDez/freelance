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
			$idAlbum = $row['idAlbum'];
            $numPhoto = mysql_num_rows(mysql_query("SELECT idPhoto FROM albums, photos WHERE photos.idAlbum=albums.idAlbum AND albums.idAlbum='$idAlbum'"));
            $albums .= '<div class="row-fluid">
                            <div class="span3">
                              <a href="albums.php?album=' . $row['idAlbum'] . '"><img src="' . $row['cover'] . '" class="img-polaroid" width="200" alt="" /></a>
                            </div>
                            <div class="span8">
                              <a href="albums.php?album=' . $row['idAlbum'] . '">' . $row['nameAlbum'] . '</a>
                              <p>' . $numPhoto . ' Foto</p>
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
        // prendo solo i numeri
        $albumID = filter_var($_GET['album'], FILTER_SANITIZE_NUMBER_INT);

        $query = mysql_query("SELECT * FROM albums WHERE idAlbum = '$albumID'") or die(mysql_error());
        $row = mysql_fetch_assoc($query);

        $addalbum .= '<a class="btn btn-info pull-left" type="submit" href="profile.php?id=' . $row['idUser'] . '"><i class="icon-arrow-left"></i>Torna al profilo</a>';


        if($row['idUser'] == $sender)
        {
            $owner = true;
            $addalbum .= '<form method="post" action="albums.php"><input type="hidden" value="' . $albumID . '" name="addFoto" /><button class="btn btn-info" type="submit"><i class="icon-plus"></i>Aggiungi foto</button></form>';
        }
        // stampo le foto ===========================================================================================================================
        $query = mysql_query("SELECT * FROM photos WHERE idAlbum='$albumID'") or die(mysql_error());
        $photo = '<div>';
        if(mysql_num_rows($query) > 0)
        {
            // ci sono le foto
            while($row = mysql_fetch_assoc($query))
            {
                $photo .= '<a href="photo.php?photo=' . $row['idPhoto'] . '"><div class="span1"><img src="' . $row['photo'] . '" class="img-polaroid" width="200px" max-height="200px" alt="a" /></div></a>';
            }
        }
        else
        {
            // non ci sono le foto
            $photo .= '<div class="well"> 0 foto </div>';
        }
        $photo .= '</div>';
        // ===============================================================================================================================================
    }
    else
    {
        if(isset($_POST['addFoto']))
        {
            $albumID = $_POST['addFoto'];
            
            $back = '<a class="btn btn-info" href="albums.php?album=' . $albumID . '"><i class="icon-arrow-left"></i> Torna all\'album</a>';

            $photo = '<section class="content">
                            <div class="container">
                                <div class="row">
                                    <div class="span8 offset2 well">
                                    <legend>Caricamento della foto</legend>
                                    <div class="span4 offset2">
                                      <form action="albums.php" enctype="multipart/form-data" method="post" id="pic" name="img">
                                        <b>Caricamento della foto</b>
                                        <div>
                                        <input type="file" name="imgField" id="imgField"  class="button"/><br/>
                                        Descrizione:
                                        <input type="text" name="descr" maxlenght="1000" /><br/>
                                        <input type="hidden" name="up" value="' . $albumID . '" />

                                        <input type="submit" value="Carica" name="imgButton" id="imgButton" class="btn btn-inverse" />
                                        </div>
                                      </form>
                                      </div>
                                    </div>
                                </div>
                            </div>
                        </section>';

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

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Album</title>
    <?php include('include.php'); ?>
    <link rel="stylesheet" type="text/css" href="style/albums.css" >
</head>

<body>
	<?php
        if(!$borders)
        {
            include('template/header.html');
        }
        ?>
        <div class="show-of-head">
            <?php
            include('template/albums.html');
            ?>
        </div>

        <?php
        if(!$borders)
        {
            include('template/footer.html');
        }
    ?>
</body>
</html>
