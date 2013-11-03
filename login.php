<?php
session_start();

if(!include('check.php'))
{
    if(isset($_POST['login']))
    {
        include_once('dbConnect.php');
        if(!isset($_POST['login']))
        {
            include_once('template/loginForm.html');
        }
        else
        {
            if(isset($_POST['user'])&&$_POST['user']==""||!isset($_POST['user']))
            {
                $error[] = "Campo username non deve essere vuoto.";
                $usererror = "1";
            }

            if(!isset($usererror))
            {
                $user = mysql_real_escape_string($_POST['user']);
                $sql = "SELECT * FROM users WHERE username = '$user'";
                if(mysql_num_rows(mysql_query($sql))=="0")
                {
                    $error[] = "Impossibile trovare utente con questo login";
                }

            }

            if(isset($_POST['pass'])&&$_POST['pass']==""||!isset($_POST['pass']))
            {
                $error[] = "Campo password non puo' essere vuoto.";
            }

            if(isset($error)) // se ci sono errori
            {
                if(is_array($error))
                {
                    $printError = '<div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>';
                    foreach ($error as $ers)
                    {
                        $printError .= "<span>".$ers."</span><br/>";
                    }
                    $printError .= '</div>';

                    include_once('template/loginForm.html');
                }
            }

            if(!isset($error)) // se non ci sono errori
            {
                // per ottenere ID e USERNAME
                $user = mysql_real_escape_string($_POST['user']);
                $pass = md5($_POST['pass']);//for secure passwords
                $find = "SELECT idUser, username, password FROM users WHERE username = '$user' AND password = '$pass'";

                // controllo del numero delle tuple
                if(mysql_num_rows(mysql_query($find)) == "1")
                {
                    session_start();


                    $result = mysql_query($find) or die(mysql_error());
                    $row = mysql_fetch_assoc($result);

                    $id = $row['idUser'];
                    $_SESSION['idUser'] = $id;

                    // set session
                    $sid = session_id();

                    // controllo se utente c'ha gia' una sessione salvata
                    if(mysql_num_rows(mysql_query("SELECT idUser FROM session WHERE idUser='$id'")) == 1)
                    {
                        // aggiorno
                        mysql_query("UPDATE session SET session='$sid' WHERE idUser='$id'") or die(mysql_error());
                    }
                    else
                    {
                        // inserisco
                        mysql_query("INSERT INTO session(idUser, session) VALUES('$id','$sid')") or die(mysql_error());
                    }

                    // setto Cookie
                    //setcookie("idC",$id,time()+364*24*60*60,"/"); //per 364 giorni

                    setcookie("PHPSESSID",$sid,time()+364*24*60*60,"/"); //per 364 giorni


                    header("Location: index.php");
                    exit();
                }
                else
                {
                    // se utente ha inserito la password sbagliata
                    $printError = '<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>';
                    $printError .= "<span>La password inserita e' sbagliata.</span><br/>";
                    $printError .= '</div>';
                    include_once('template/loginForm.html');
                }
            }
        }
    }
    else
    {
        include_once('template/loginForm.html');
    }
}
else
{
    header('location:index.php');
    exit();
}
?>