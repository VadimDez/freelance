<?php
    session_start();

    //setcookie("PHPSESSID","",time()-3600,"/");

	include_once('model.php');
	include_once('check.php');
	$home = new home;
	$conn = new myConnection;
    $pars = new parser();
	$conn->connect();

    // categorie
	$categorie = $home->categorie();
	//$projects = $home->annunci();

    // progetti
	if(isset($_GET['page']))
	{
        // prendo solo i numeri
        $page = filter_var($_GET['page'], FILTER_SANITIZE_NUMBER_INT);

        // se non ci sono numeri - la stringa diventa vuota
        if($page == '')
        {
            // e se vuota -
            $page = 1;
        }

        $projects = $home->annunci($page);
		$pages = $home->pageCounter($page);
	}
	else
	{
        $projects = $home->annunci();
		$pages = $home->pageCounter();
	}


	$conn->close();

    $randomUsers = $home->randomUsers();

?>

<!doctype html>
<html lang="it">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>iFreelancer.it - Lavoro, freelance e sponsorizzazione in Italia</title>
        <?php include_once('include.php'); ?>
        <script type="text/javascript" src="js/bootstrap-carousel.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.carousel').carousel();
            });
        </script>
    </head>
    <body id="render">
    <div id="fb-root"></div>
            <?php
				include_once('template/header.html');
			 	include_once('template/main.html');
				include_once('template/footer.html');
			?>
    </body>
</html>