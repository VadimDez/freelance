<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 3/18/13
 * Time: 9:05 PM
 * To change this template use File | Settings | File Templates.
 */


session_start();
if(include('../check.php'))
{
    if(isset($_POST['idProj']) && $_POST['idProj'] != "" && isset($_POST['type']))
    {
        // db connect
        include('../model.php');
        $parser = new parser();
        $connect = new myConnection;
        $connect->connect();

        $idUser = $_SESSION['idUser'];
        $idProj = $_POST['idProj'];

        // controllo
        if(mysql_num_rows(mysql_query("SELECT idProj FROM project WHERE idProj='$idProj'")) == 1)
        {
            if($_POST['type'] == "spons" && isset($_POST['text']))
            {
                $text   = $_POST['text'];
                $text   = $parser->textParsingWithNL($text);
                mysql_query("INSERT INTO candidati(idProj, idUser, data, comment) VALUES('$idProj', '$idUser', now(), '$text')") or die(mysql_error());
            }
            else
            {
                if($_POST['type'] == "left")
                {
                    mysql_query("DELETE FROM candidati WHERE idUser='$idUser' AND idProj='$idProj'") or die(mysql_error());

                    if(mysql_num_rows(mysql_query("SELECT * FROM vincenti WHERE idUser='$idUser' AND idProj='$idProj'")) > 0)
                    {
                        mysql_query("DELETE FROM vincenti WHERE idUser='$idUser' AND idProj='$idProj'");
                    }
                }
                else
                {
                    if($_POST['type'] == "close")
                    {
                        mysql_query("UPDATE project SET closed='2' WHERE idProj='$idProj'") or die(mysql_error());
                    }
                    else
                    {
                        if($_POST['type'] == "select")
                        {
                            if(mysql_num_rows(mysql_query("SELECT idUser FROM project WHERE idUser='$idUser' AND idProj='$idProj'")) == 1)
                            {
                                if(isset($_POST['pay']) && $_POST['pay'] != '')
                                {
                                    $candidato = $_POST['idUser'];
                                    $pay = $_POST['pay'];
                                    $pay = $parser->textParsing($pay);

                                    if(mysql_num_rows(mysql_query("SELECT idUser FROM candidati WHERE idProj='$idProj' AND idUser='$candidato'")) == 1)
                                    {
                                        mysql_query("INSERT INTO vincenti(idUser,idProj,paypal) VALUES('$candidato', '$idProj','$pay')") or die(mysql_error());
                                        mysql_query("UPDATE project SET closed='1' WHERE idProj='$idProj'") or die(mysql_error());
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        // chiudo connessione
        $connect->close();
        //echo json_encode($risultato);
    }
}
else
{
    header('../registration.php');
}



?>