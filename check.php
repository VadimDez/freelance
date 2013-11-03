<?php

    // controllo per riprendere la sessione;
    session_start();
    if(isset($_SESSION['idUser']))
    {
        return true;
    }
    else
    {
        /*if(isset($_COOKIE['idC']))
        {
            $_SESSION['idUser'] = $_COOKIE['idC'];
            return true;
        }
        else
        {
            return false;
        }*/
        include_once('model.php');

        $connect = new myConnection();
        if($connect->connect())
        {
            $sid = session_id();
            $check = mysql_query("SELECT idUser FROM session WHERE session='$sid'") or die(mysql_error());
            if( mysql_num_rows($check) == 1)
            {
                $row = mysql_fetch_assoc($check);
                $_SESSION['idUser'] = $row['idUser'];
                $connect->close();
                return true;
            }
            else
            {
                $connect->close();
                return false;
            }
        }
        $connect->close();
        return false;
    }
?>