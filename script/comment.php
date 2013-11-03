<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 3/28/13
 * Time: 10:54 AM
 * To change this template use File | Settings | File Templates.
 */
session_start();
if(include('../check.php'))
{
    if(isset($_POST['idProj']) && isset($_POST['idProp']) && isset($_POST['text']))
    {
        if($_POST['idProj'] != '' && $_POST['idProp'] != ''  && $_POST['text'] != '')
        {
            $idProj = $_POST['idProj'];
            $idProp = $_POST['idProp'];
            include_once('../model.php');
            $conn = new myConnection();
            if($conn->connect())
            {
                $usr = mysql_query("SELECT idUser FROM candidati WHERE idProposta='$idProp'") or die(mysql_error());
                $usr = mysql_fetch_assoc($usr);
                $usr = $usr['idUser'];

                $admin = mysql_query("SELECT idUser FROM project WHERE idProj='$idProj'") or die(mysql_error());
                $admin = mysql_fetch_assoc($admin);
                $admin = $admin['idUser'];
                if($usr == $_SESSION['idUser'] || $admin == $_SESSION['idUser'])
                {
                    $parser = new parser();
                    $text = $parser->textParsingWithNL($_POST['text']);
                    $idUser = $_SESSION['idUser'];
                    mysql_query("INSERT INTO comments(idProposta,idProj,idUser,comment,data) VALUES('$idProp','$idProj','$idUser','$text',now())") or die(mysql_error());
                }
                $conn->close();
            }
        }
    }
}

?>