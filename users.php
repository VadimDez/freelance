<?php
session_start();
include('model.php');
$parser    = new parser();
$takeUsers = new users();
$connection= new myConnection();
$connection->connect();
if(isset($_GET['t']) && isset($_GET['page']))
{
    // se passa tipo e pagina
    $page = $parser->textParsing($_GET['page']);
    // prendo solo i numeri
    $page = filter_var($page, FILTER_SANITIZE_NUMBER_INT);

    // se non ci sono numeri - la stringa diventa vuota
    if($page == '')
    {
        // e se vuota -
        $page = 1;
    }
    $tipo = $parser->textParsing($_GET['t']);

    $users = $takeUsers->showUsers($tipo,$page);
    $pageCounter = $takeUsers->pageCounter($page,$tipo);
}
else
{
    if(isset($_GET['page']))
    {
        // se passsa solo pagina cioe' sta guardando tutti
        $page = $parser->textParsing($_GET['page']);
        // prendo solo i numeri
        $page = filter_var($page, FILTER_SANITIZE_NUMBER_INT);

        // se non ci sono numeri - la stringa diventa vuota
        if($page == '')
        {
            // e se vuota -
            $page = 1;
        }
        $users = $takeUsers->showUsers("NULL",$page);
        $pageCounter = $takeUsers->pageCounter($page,"NULL");
    }
    else
    {
        if(isset($_GET['t']))
        {
            // se passa solo il tipo
            $tipo = $parser->textParsing($_GET['t']);

            $users = $takeUsers->showUsers($tipo,1);
            $pageCounter = $takeUsers->pageCounter(1,$tipo);
        }
        else
        {
            $users = $takeUsers->showUsers();
            $pageCounter = $takeUsers->pageCounter();
        }
    }
}
$connection->close();
?>

<!DOCTYPE HTML>
<html lang="it">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Utenti</title>
    <?php include('include.php'); ?>
    <!-- ricerca -->
    <script type="text/javascript">
        function find()
        {
            if(window.XMLHttpRequest)
            {
                xmlhttp = new XMLHttpRequest();
            }
            else
            {
                xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');
            }

            xmlhttp.onreadystatechange = function()
            {
                if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
                {
                    document.getElementById('result').innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open('GET', 'searchFriend.php?search_name='+document.search.search_name.value, true);
            xmlhttp.send();
        }
    </script>


</head>
<body>
<?php
include_once('template/header.html');
include('template/users.html');
include_once('template/footer.html');
?>
</body>

</html>