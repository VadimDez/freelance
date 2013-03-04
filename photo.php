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
    $idPhoto = $_GET['photo'];
    
    $query = mysql_query("SELECT * FROM photos WHERE idPhoto = '$idPhoto'") or die(mysql_error());
    if(mysql_num_rows($query) == 1)
    {
        $query = mysql_fetch_assoc($query);
        $photo = '<img src="' . $query['photo'] . '">';
        $back = '<a href="albums.php?album=' . $query['idAlbum'] . '">Torna all\'album</a>';
        $descr = $query['descr'];
        
        $dataPhoto = $query['dataPhoto'];
        // la foto precedente 
        $query2 = mysql_query("SELECT * FROM photos WHERE dataPhoto < '$dataPhoto' ORDER BY dataPhoto DESC LIMIT 1") or die(mysql_error());
        if(mysql_num_rows($query2) > 0)
        {
            $row    = mysql_fetch_assoc($query2);
            $prev = 'photo.php?photo=' . $row['idPhoto'];
        }
        
        // la foto successiva 
        $query2 = mysql_query("SELECT * FROM photos WHERE dataPhoto > '$dataPhoto' ORDER BY dataPhoto ASC LIMIT 1") or die(mysql_error());
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

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Untitled Document</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
   	<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
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