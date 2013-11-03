<?php

if(include('check.php'))
{

    include('dbConnect.php'); // definizioni della database;
    include_once('model.php');
    $parser = new parser();
    $idUser = $_SESSION['idUser'];

    $msg = ""; // messaggio che utente vede dopo la modifica, puo' essere un errore o un successo

// parsing
    if(isset($_POST['changed']))
    {
        // FOTO del profilo
        if($_POST['changed'] == "imgChanged")
        {
            if(!$_FILES['imgField']['tmp_name'])
            {
                $msg = '<font color="red">Prima devi selezionare l\'immagine.</font>';
            }
            else
            {
                $maxsize = 10000000; // Utente puo' scegliere al massimo l'immagine che e' minore di 10MB
                if($_FILES['imgField']['size'] > $maxsize)
                {
                    $msg = '<font color=\"red\">Deve scegliere l\'immagine che ha grandezza minore di 10MB.</font>';
                    unlink($_FILES['imgField']['tmp_name']);
                }
                else
                {
                    if(!preg_match("/\.(gif|jpg|png|jpeg)$/i",$_FILES['imgField']['name']))
                    {
                        $msg = "<font color=\"red\">Deve scegliere l'immagine di tipo GIF,JPG o PNG.</font>";
                        unlink($_FILES['imgField']['tmp_name']);
                    }
                    else
                    {
                        $newImg = "profilePhoto/$idUser.jpg";
                        $newfile = move_uploaded_file($_FILES['imgField']['tmp_name'],$newImg);
                        $msg = '<font color="green">L\'immagine del profile e\' modificata.</font>';
                        // query per caricare indirizzo dell'immagine nella database
                        $query = mysql_query("UPDATE users SET img='$newImg' WHERE idUser='$idUser'");
                    }
                }
            }
        }
        // fine foto;
        else
        {
            // Nome e Cognome del utente
            if($_POST['changed'] == "nameChanged")
            {
                if(strlen($_POST['name']) < 3)
                {
                    $msg = "<font color=\"red\">Campo nome deve contenere piu' di due caratteri.</font>";
                }
                else
                {
                    if(strlen($_POST['secondname']) < 3)
                    {
                        $msg = "<font color=\"red\">Campo cognome deve contenere piu' di due caratteri.</font>";
                    }
                    else
                    {
                        $newName       = $_POST['name'];
                        //parsing
                        $newName = stripslashes($newName);
                        $newName = strip_tags($newName);
                        $newName = mysql_real_escape_string($newName);
                        $newName = trim($newName, '\r\n');
                        $newName = preg_replace('/\'/i','&#39;', $newName);
                        $newName = preg_replace('/`/i','', $newName);
                        $newName = mysql_real_escape_string($newName);


                        $newSecondname = $_POST['secondname'];
                        //parsing
                        $newSecondname = stripslashes($newSecondname);
                        $newSecondname = strip_tags($newSecondname);
                        $newSecondname = mysql_real_escape_string($newSecondname);
                        $newSecondname = trim($newSecondname, '\r\n');
                        $newSecondname = preg_replace('/\'/i','&#39;', $newSecondname);
                        $newSecondname = preg_replace('/`/i','', $newSecondname);
                        $newSecondname = mysql_real_escape_string($newSecondname);


                        if(mysql_query("UPDATE users SET name='$newName', secondname='$newSecondname' WHERE idUser='$idUser'") && ($newName != "" && $newSecondname !=""))
                        {
                            $msg = '<font color="green">Nome e/o cognome e\' stato cambiato.</font>';
                        }
                        else
                        {
                            $msg = '<font color="red">Errore.</font>';
                        }
                    }
                }
            }
            else
            {
                //La citta' ed il paese:
                if($_POST['changed'] == "countryChanged")
                {
                    if(strlen($_POST['city']) < 3)
                    {
                        $msg = "<font color=\"red\">Campo citta' deve contenere piu' di due caratteri.</font>";
                    }
                    else
                    {

                        $newCity    = $_POST['city'];
                        // qui cancello ` e cambio le ' perche puo' essere il conflitto con la database
                        $newCity = stripslashes($newCity);
                        $newCity = strip_tags($newCity);
                        $newCity = mysql_real_escape_string($newCity);
                        $newCity = trim($newCity, '\r\n');
                        $newCity = preg_replace('/\'/i','&#39', $newCity);
                        $newCity = preg_replace('/`/i','', $newCity);
                        $newCity = mysql_real_escape_string($newCity);





                        if(mysql_query("UPDATE users SET city='$newCity' WHERE idUser='$idUser'"))
                        {
                            $msg = "<font color=\"green\">La citta' e' stata cambiata.</font>";
                        }
                        else
                        {
                            $msg = "<font color=\"red\">Errore.</font>";
                        }
                    }
                }
                else
                {
                    // info
                    if($_POST['changed'] == "aboutChanged")
                    {
                        $newAbout = $_POST['about'];
                        /*
                        $newAbout = stripslashes($newAbout);
                        $newAbout = strip_tags($newAbout);
                        $newAbout = mysql_real_escape_string($newAbout);
                        $newAbout = preg_replace('/`/i','', $newAbout);
                         */
                        $newAbout = str_replace("\r\n", "<br />", $newAbout);
                        $newAbout = str_replace("\n", "<br />", $newAbout);
                        $newAbout = str_replace("\r", "<br />", $newAbout);
                        $newAbout = stripslashes($newAbout);
                        $newAbout = strip_tags($newAbout, '<br>');
                        $newAbout = mysql_real_escape_string($newAbout);

                        $newAbout = preg_replace('/\'/i','&#39;', $newAbout);
                        $newAbout = preg_replace('/`/i','&#96;', $newAbout);
                        $newAbout = mysql_real_escape_string($newAbout);
                        if(mysql_query("UPDATE users SET about='$newAbout' WHERE idUser='$idUser'"))
                        {
                            $msg = "<font color=\"green\">La tua informazione e' stata cambiata.</font>";
                        }
                        else
                        {
                            $msg = "<font color=\"red\">Errore.</font>";
                        }
                    }
                    else
                    {
                        // password
                        if($_POST['changed'] == "passChanged")
                        {
                            $oldPass = md5($_POST['oldPass']);
                            if(mysql_num_rows(mysql_query("SELECT * FROM users WHERE password = '$oldPass' AND idUser = '$idUser' ")) >  0)
                            {
                                // cambio la pass
                                $newPass1 = $_POST['newPass1'];
                                $newPass2 = $_POST['newPass2'];
                                if($newPass1 == $newPass2)
                                {
                                    // cambio
                                    $newPass1 = md5($newPass1);
                                    if(mysql_query("UPDATE users SET password = '$newPass1' WHERE idUser = '$idUser' "))
                                    {
                                        $msg = '<font color="green">La tua password e* stata cambiata.</font>';
                                    }
                                    else
                                    {
                                        $msg = '<font color="red">Errore.</font>';
                                    }
                                }
                                else
                                {
                                    $msg = '<font color="red">La nuova password scritta male.</font>';
                                }
                            }
                            else
                            {
                                // errore
                                $msg = '<font color="red">La password sbagliata.</font>';
                            }
                        }
                        else
                        {
                            if($_POST['changed'] == "birthChanged")
                            {
                                $day 	= $_POST['day'];
                                $day = $parser->textParsing($day);
                                $month 	= $_POST['month'];
                                $month = $parser->textParsing($month);
                                $year 	= $_POST['year'];
                                $year = $parser->textParsing($year);
                                mysql_query("UPDATE users SET day='$day', month='$month', year='$year' WHERE idUser =  '$idUser'") or die(mysql_error());
                                $msg = '<font color="green">La data di nascita e* stata cambiata.</font>';
                            }
                        }
                    }
                }
            }
        }
    }
// prende le info di utente
    include('pick.php');
    // trasformo br in \n
    $about = preg_replace('/<br[^>]*?>/si',"\n",$about);

}
else
{

}

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        Modifica del profilo di <?php print "$name"; ?>
    </title>
    <?php include('include.php'); ?>
    <script type="text/javascript" src="script/text.js"></script>
</head>
<body onload="init()">



<?php
include('template/header.html');
include('template/editProfile.html');
include_once('template/footer.html');
?>
</body>
</html>

