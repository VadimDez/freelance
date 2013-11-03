<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 3/22/13
 * Time: 9:00 PM
 */
session_start();

if(include_once('check.php'))
{
    if(isset($_POST['name']) && isset($_POST['email']) && isset($_POST['message']))
    {
        if($_POST['name'] != '' && $_POST['email'] != '' && $_POST['message'] != '')
        {
            $to      = 'vasp3d@gmail.com';
            $subject = 'ifreelance.it';
            $message = $_POST['message'];
            $headers = 'From:' . $_POST['email'] . "\r\n";
            if(mail($to, $subject, $message, $headers))
            {
                // e' stato spedito
                $msg = '<div class="alert alert-success">
                    Messaggio e\' stato spedito.
               </div>';
            }
            else
            {
                $msg = '<div class="alert alert-block">
                    Errore nel processo di spedizione della mail.
               </div>';
            }
        }
        else
        {
            $msg = '<div class="alert alert-block">
                    Riempi tutti i campi.
               </div>';
        }

    }
    include_once('template/contact.html');
}
else
{
    header('location:login.php');
    exit();
}



?>