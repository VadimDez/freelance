<?php
session_start();
include('model.php');

$takeUsers = new users();

if(isset($_GET['t']) && isset($_GET['page']))
{
    // se passa tipo e pagina
    $users = $takeUsers->showUsers($_GET['t'],$_GET['page']);
    $pageCounter = $takeUsers->pageCounter($_GET['page'],$_GET['t']);
}
else
{
    if(isset($_GET['page']))
    {
        // se passsa solo pagina cioe' sta guardando tutti
        $users = $takeUsers->showUsers("NULL",$_GET['page']);
        $pageCounter = $takeUsers->pageCounter($_GET['page'],"NULL");
    }
    else
    {
        if(isset($_GET['t']))
        {
            // se passa solo il tipo
            $users = $takeUsers->showUsers($_GET['t'],1);
            $pageCounter = $takeUsers->pageCounter(1,$_GET['t']);
        }
        else
        {
            $users = $takeUsers->showUsers();
            $pageCounter = $takeUsers->pageCounter();
        }
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Registrazione</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="style/main.css" >
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
<script type="text/javascript" src="script/jquery-1.8.3.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
</body>

</html>