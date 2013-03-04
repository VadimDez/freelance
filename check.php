<?php
    //
    session_start();
    if(isset($_SESSION['idUser']))
    {
        if(!isset($_COOKIE['idUser']))
        {
            return true;
        }
        else
        {
            $_SESSION['idUser'] = $_COOKIE['idUser'];
        }
    }
    else
    {
        return false;
    }
?>