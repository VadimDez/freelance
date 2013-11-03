<?php  

$ipn_post_data = $_POST;

// Scelta url (sandbox o no)
if(array_key_exists('test_ipn', $ipn_post_data) && 1 === (int) $ipn_post_data['test_ipn'])
    $url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
else
    $url = 'https://www.paypal.com/cgi-bin/webscr';

// Prendo la richiesta di paypal
$notify_string = "cmd=_notify-validate&".$ipn_post_data;

$request = curl_init();
curl_setopt($request, CURLOPT_URL, $url);
curl_setopt($request, CURLOPT_HEADER, TRUE);
curl_setopt($request, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($request, CURLOPT_POST, TRUE);
curl_setopt($request, CURLOPT_POSTFIELDS, $notify_string);
curl_setopt($request, CURLOPT_FOLLOWLOCATION, FALSE);
curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($request, CURLOPT_TIMEOUT, 30);

// Eseguo la richiesta e ottengo lo status
$response = curl_exec($request);
$status   = curl_getinfo($request, CURLINFO_HTTP_CODE);
curl_close($request);

if($status == 200 && strcasecmp($ipn_post_data['payment_status'], "Completed")=="0"){
    /* Richiesta di pagamento OK */

    // trasformo tutto in utf-8
    if(array_key_exists('charset', $ipn_data) && ($charset = $ipn_data['charset'])){
        if($charset == 'utf-8')
            return;

        foreach($ipn_data as $key => &$value)
        {
            $value = mb_convert_encoding($value, 'utf-8', $charset);
        }

        $ipn_data['charset'] = 'utf-8';
        $ipn_data['charset_original'] = $charset;
    }

    /*
    * Paypal IPN variabili: (https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_html_IPNandPDTVariables)
    */

    /*
    * 1) Controllo la variabile txn_id se Ë gi‡ stata usata
    * Questo controllo serve ad evitare doppie procedure (nel caso di azioni refresh) e quindi doppi pagamenti! La soluzione migliore Ë archiviare ogni id
    * transazione nel DB e controllarne ogni volta la presenza! E' perfetto per pagamenti a tempo (eCheck ad esempio) per aggiornare la transazione dopo
    * un certo periodo!
    */

    /*
    * 2) Controllo se l'email ricevente Ë la stessa del form
    * In questo modo controllo l'autenticit‡ della transazione e non rischio problemi.
    */

    /*
    * 3) Eseguo codice per transazione andata a buon fine
    * A questo punto posso eseguire il codice necessario!
    */

    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];

    if($txn_id == 'RNRHWM5BF3HW8')
    {

        include('../model.php');
        $connect = new myConnection();
        if($connect->connect())
        {
            $to      = 'vasp3d@gmail.com';
            $subject = 'Download Area | Login Credentials';
            $message = 'ok ' . $payer_email. "\r\n" . $item_name. "\r\n" . $item_number. "\r\n"  . $payment_status. "\r\n" . $payment_amount . "\r\n". $payment_currency. "\r\n" . $txn_id. "\r\n" . $receiver_email. "\r\n" . $payer_email. "\r\n";
            $headers = 'From:noreply@yourdomain.com' . "\r\n";
            mail($to, $subject, $message, $headers);

            mysql_query("INSERT INTO paid(idPaid, idSeller,item_name, idProj, payment_status, payment_amount, receiver_email, payer_email, date) VALUES(NULL, '$txn_id', '$item_name', '$item_number', '$payment_status', '$payment_amount', '$receiver_email', '$payer_email', now())") or die(mysqL_error());
            mysql_query("UPDATE project SET ownerPaid='1' WHERE idProj='$item_number'") or die(mysql_error());
        }


        $connect->close();

    }


}else{
    /* PAGAMENTO NON COMPLETATO O CON ERRORI */
    $to      = 'vasp3d@gmail.com';
    $subject = 'Download Area | Login Credentials';
    $message = 'bad';
    $headers = 'From:noreply@yourdomain.com' . "\r\n";
    mail($to, $subject, $message, $headers);
}
?>