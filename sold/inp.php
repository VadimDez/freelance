<?php  
	//mysql_connect("localhost", "user", "password") or die(mysql_error());  
	//mysql_select_db("PayPal") or die(mysql_error());  
	// read the post from PayPal system and add 'cmd'  
	/*$req = 'cmd=_notify-validate';  
	foreach ($_POST as $key => $value) {  
	$value = urlencode(stripslashes($value));  
	$req .= "&$key=$value";  
	}  
	// post back to PayPal system to validate  
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";  
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";  
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";  
	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);  
	if (!$fp) {  
	// HTTP ERROR  
	} else {  
	fputs ($fp, $header . $req);  
	while (!feof($fp)) {  
	$res = fgets ($fp, 1024);  
	if (strcmp ($res, "VERIFIED") == 0) {  
		// PAYMENT VALIDATED & VERIFIED!  
		$email = $_POST['payer_email'];
		$password = mt_rand(1000, 9999);  
		//mysql_query("INSERT INTO users (email, password) VALUES('". mysql_escape_string($email) ."', '".md5($password)."' ) ") or die(mysql_error());  
		$to      = $email;
		$subject = 'Download Area | Login Credentials';  
		$message = ' 
		Thank you for your purchase 
		Your account information 
		------------------------- 
		Email: '.$email.' 
		Password: '.$password.' 
		------------------------- 
		You can now login at http://yourdomain.com/PayPal/';  
		$headers = 'From:noreply@yourdomain.com' . "\r\n";  
		mail($to, $subject, $message, $headers);   
	}  
	else if (strcmp ($res, "INVALID") == 0) {  
	// PAYMENT INVALID & INVESTIGATE MANUALY!
	
		$to      = 'vasp3d@gmail.com';  
		$subject = 'Download Area | Invalid Payment';  
		$message = ' 
		Dear Administrator, 
		A payment has been made but is flagged as INVALID. 
		Please verify the payment manualy and contact the buyer. 
		Buyer Email: '.$email.' 
		';  
		$headers = 'From:noreply@yourdomain.com' . "\r\n";  
		mail($to, $subject, $message, $headers); 
	  
	}  
	}  
	fclose ($fp);  
	} */
	
	// PHP 4.1
			
			// read the post from PayPal system and add 'cmd'
			$req = 'cmd=_notify-validate';
			
			foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
			}
			
			// post back to PayPal system to validate
			$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
			$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
			
			// assign posted variables to local variables
			$item_name = $_POST['item_name'];
			$item_number = $_POST['item_number'];
			$payment_status = $_POST['payment_status'];
			$payment_amount = $_POST['mc_gross'];
			$payment_currency = $_POST['mc_currency'];
			$txn_id = $_POST['txn_id'];
			$receiver_email = $_POST['receiver_email'];
			$payer_email = $_POST['payer_email'];
			
			if (!$fp) {
			// HTTP ERROR
			} else {
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
			$res = fgets ($fp, 1024);
			if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment
			
			$password = mt_rand(1000, 9999);  
			//mysql_query("INSERT INTO users (email, password) VALUES('". mysql_escape_string($email) ."', '".md5($password)."' ) ") or die(mysql_error());  
			$to      = 'vasp3d@gmail.com'; 
			$subject = 'Download Area | Login Credentials';  
			$message = ' 
			Thank you for your purchase 
			Your account information 
			------------------------- 
			------------------------- 
			You can now login at http://yourdomain.com/PayPal/';  
			$headers = 'From:noreply@yourdomain.com' . "\r\n";  
			mail($to, $subject, $message, $headers);  
			
			//mysql_query("UPDETE project SET ownerPay='1' WHERE idProj='$item_number'") or die(mysql_error());
			
			
			}
			else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
				$to      = 'vasp3d@gmail.com';  
				$subject = 'Download Area | Invalid Payment';  
				$message = ' 
				Dear Administrator, 
				A payment has been made but is flagged as INVALID. 
				Please verify the payment manualy and contact the buyer. 
				Buyer Email: '.$email.' 
				';  
				$headers = 'From:noreply@yourdomain.com' . "\r\n";  
				mail($to, $subject, $message, $headers); 
			}
			}
			fclose ($fp);
			}
			////////////////////////////////////////////////////////////////////////////////////////////////
			
		/*	
 
// STEP 1: Read POST data
 
// reading posted data from directly from $_POST causes serialization 
// issues with array data in POST
// reading raw POST data from input stream instead. 
$raw_post_data = file_get_contents('php://input');
$raw_post_array = explode('&', $raw_post_data);
$myPost = array();
foreach ($raw_post_array as $keyval) {
  $keyval = explode ('=', $keyval);
  if (count($keyval) == 2)
     $myPost[$keyval[0]] = urldecode($keyval[1]);
}
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
if(function_exists('get_magic_quotes_gpc')) {
   $get_magic_quotes_exists = true;
} 
foreach ($myPost as $key => $value) {        
   if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) { 
        $value = urlencode(stripslashes($value)); 
   } else {
        $value = urlencode($value);
   }
   $req .= "&$key=$value";
}
 
 
// STEP 2: Post IPN data back to paypal to validate
 
$ch = curl_init('https://www.paypal.com/cgi-bin/webscr');
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
 
// In wamp like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "http://curl.haxx.se/docs/caextract.html" and set the directory path 
// of the certificate as shown below.
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
if( !($res = curl_exec($ch)) ) {
    // error_log("Got " . curl_error($ch) . " when processing IPN data");
    curl_close($ch);
    exit;
}
curl_close($ch);
 
 
// STEP 3: Inspect IPN validation result and act accordingly
 
if (strcmp ($res, "VERIFIED") == 0) {
    // check whether the payment_status is Completed
    // check that txn_id has not been previously processed
    // check that receiver_email is your Primary PayPal email
    // check that payment_amount/payment_currency are correct
    // process payment
 
    // assign posted variables to local variables
    $item_name = $_POST['item_name'];
    $item_number = $_POST['item_number'];
    $payment_status = $_POST['payment_status'];
    $payment_amount = $_POST['mc_gross'];
    $payment_currency = $_POST['mc_currency'];
    $txn_id = $_POST['txn_id'];
    $receiver_email = $_POST['receiver_email'];
    $payer_email = $_POST['payer_email'];
	
	$to      = 'vasp3d@gmail.com'; 
			$subject = 'Download Area | Login Credentials';  
			$message = ' 
			Thank you for your purchase 
			Your account information 
			------------------------- 
			------------------------- 
			You can now login at http://yourdomain.com/PayPal/';  
			$headers = 'From:noreply@yourdomain.com' . "\r\n";  
			mail($to, $subject, $message, $headers);  
	
} else if (strcmp ($res, "INVALID") == 0) {
    // log for manual investigation
	$to      = 'vasp3d@gmail.com';  
				$subject = 'Download Area | Invalid Payment';  
				$message = ' 
				Dear Administrator, 
				A payment has been made but is flagged as INVALID. 
				Please verify the payment manualy and contact the buyer. 
				Buyer Email: '.$email.' 
				';  
				$headers = 'From:noreply@yourdomain.com' . "\r\n";  
				mail($to, $subject, $message, $headers); 
}
*/
?>  