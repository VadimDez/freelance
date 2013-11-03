<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 3/5/13
 * Time: 10:07 PM
 * To change this template use File | Settings | File Templates.
 */

	if(isset($_GET['id']))
    {
        $idUser = $_GET['id'];
    }
    else
    {
        if(isset($_SESSION['idUser']))
        {
            $idUser = $_SESSION['idUser'];
        }
        else
        {
            header('location:index.php');
            exit();
        }
    }

	if($idUser)
    {
        include('dbConnect.php');
        $query = mysql_query("SELECT * FROM feedback WHERE idReceiver='$idUser' ORDER BY dataFeed") or die(mysql_error());
        if(mysql_num_rows($query) == 0)
        {
            $risultato = "Non ci sono feedback";
        }
        else
        {
            while($row = mysql_fetch_assoc($query))
            {
                $idProj     = $row['idProj'];
                $text       = $row['text'];
                $value      = $row['value'];
                $data = strftime("%d %b %Y", strtotime($row['dataFeed']));
                $idSender   = $row['idSender'];

                $query2 = mysql_fetch_assoc(mysql_query("SELECT name, secondname FROM users WHERE idUser='$idSender'"));

                if($value == '+')
                {
                    $value = 'success';
                }
                else
                {
                    $value = 'error';
                }
                $risultato .= '<tr class="' . $value . '"><td>' . $data . '</td><td><a href="profile.php?id=' . $idSender . '">' . $query2['name'] . ' ' . $query2['secondname'] . '</a></td><td>' . $text . '</td></tr>';
            }
        }
    }
    else
    {
        header('location:index.php');
        exit();
    }


?>

<div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>Data</th>
            <th>Utente</th>
            <th>Testo</th>
        </tr>
        </thead>
        <tbody>
        <?php print $risultato; ?>
        </tbody>
    </table>
</div>

