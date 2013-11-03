<?php
include('check.php'); 
// prende le info di utente
include_once('pick.php');

$photo = '';
$back  = '';
$next  = '';
$prev  = '';
$descr = '';
if(isset($_GET['photo']))
{

    // prendo solo i numeri
    $idPhoto = filter_var($_GET['photo'], FILTER_SANITIZE_NUMBER_INT);
    
    $query = mysql_query("SELECT * FROM photos WHERE idPhoto = '$idPhoto'") or die(mysql_error());
    if(mysql_num_rows($query) == 1)
    {
        $query = mysql_fetch_assoc($query);
        // idalbum
        $idAlbum = $query['idAlbum'];
        $photo = '<img src="' . $query['photo'] . '" class="img-polaroid">';
        $back = '<a href="albums.php?album=' . $query['idAlbum'] . '" class="btn btn-info"><i class="icon-arrow-left"></i> Torna all\'album</a>';
        $descr = $query['descr'];
        
        $dataPhoto = $query['dataPhoto'];
        // la foto precedente 
        $query2 = mysql_query("SELECT idPhoto FROM photos WHERE dataPhoto < '$dataPhoto' AND idAlbum='$idAlbum' ORDER BY dataPhoto DESC LIMIT 1") or die(mysql_error());
        if(mysql_num_rows($query2) > 0)
        {
            $row    = mysql_fetch_assoc($query2);
            $prev = 'photo.php?photo=' . $row['idPhoto'];
        }
        
        // la foto successiva 
        $query2 = mysql_query("SELECT idPhoto FROM photos WHERE dataPhoto > '$dataPhoto' AND idAlbum='$idAlbum' ORDER BY dataPhoto ASC LIMIT 1") or die(mysql_error());
        if(mysql_num_rows($query2) > 0)
        {
            $row    = mysql_fetch_assoc($query2);
            $next = 'photo.php?photo=' . $row['idPhoto'];
        }
        
    }
    else
    {
        header('Location:albums.php');
        exit;
    }
    
}
else
{
    header('Location:albums.php');
    exit;
}
mysql_close();
?>

<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Portofolio</title>
    <link rel="shortcut icon" href="assets/favicon.png" />
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap-responsive.css" rel="stylesheet" type="text/css">
    <link href="css/render.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="script/jquery-1.8.3.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="css/fonts.css" type='text/css'>
	<link rel="stylesheet" type="text/css" href="style/main.css" >
</head>

<body>
<?php
	include('template/header.html');
	include('template/photo.html');
    include('template/footer.html');
?>
<script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>