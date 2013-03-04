<?php
    session_start();
	include('dbConnect.php');
	include('model.php');
	
	$home = new home;
	$conn = new myConnection;
	$conn->connect();
	
    // categorie
	$categorie = $home->categorie();
	$projects = $home->annunci();
	
	if(isset($_GET['page']))
	{
        $projects = $home->annunci($_GET['page']);
		$pages = $home->pageCounter($_GET['page']);
	}
	else
	{
        $projects = $home->annunci();
		$pages = $home->pageCounter();
	}
	$conn->close();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Home page</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
   		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <link rel="stylesheet" type="text/css" href="style/main.css" >
    </head>
    <body>
            <?php
				include('template/header.html');
			 	include('template/main.html'); 
				include('template/footer.html'); 
			?>
            <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>