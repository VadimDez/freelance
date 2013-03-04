<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="it" xmlns="http://www.w3.org/1999/xhtml">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Registrazione</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
   		<link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body >

<?php
	if(!@include('check.php'))
	{
		@include('template/header.html');
		include('dbConnect.php');
		if(!isset($_POST['register']))
		{
			include('template/registrationForm.html');
			include('template/footer.html');
		}
		else
		{
			
			if(isset($_POST['name']) && $_POST['name']=="" || !isset($_POST['name']))
			{
				$error[] = "Inserisci il tuo nome!";
			}
			
			if(!isset($_POST['secondname']))
			{
				$error[] = "Error cognome";
			}
			else if(isset($_POST['secondname']) && $_POST['secondname']=="" && $_POST['tipo'] == "0")
			{
				$error[] = "Inserisci il tuo cognome, deve essere maggiore di 2 caratteri";
			}
			else
			{
				if(strlen($_POST['secondname']) < 3)
				{
					$errore[] = "Cognome deve essere piu' lungo di due lettere";
				}
			}
			
			
			if (strlen($_POST['user']) < 3 || strlen($_POST['user']) > 18 || !preg_match('/^[0-9a-zA-Z]+$/',$_POST['user']) || !isset($_POST['user']))
			{
				
				$error[] = "Login non valido, deve contenere 3 - 18 caratteri, solo 0-9,a-Z";
				$usererror = "1";
				
			}
			
			
			if(!isset($usererror))
			{
				$user = mysql_real_escape_string($_POST['user']);
				$sql = "SELECT * FROM users WHERE username='$user'";
				
				if(mysql_num_rows(mysql_query($sql))=="1")
				{
					$error[] = "Login gia' usato."; 
				}
			}
	
			if(isset($_POST['password'])&&$_POST['password']==""||!isset($_POST['password']))
			{
				$error[] = "Inserisci la password."; 
			}
			
			if(!isset($_POST['tipo']))
			{
				$error[] = "Scegli il tipo."; 
			}
			
			if(isset($error))
			{
				if(is_array($error))
				{
					print "<br /><div>";
					foreach ($error as $ers)
					{
	
						echo "<span>".$ers."</span><br/>";
	
					}
					echo "</div>";
					print "<br />";
					include('template/registrationForm.html');
					include('template/footer.html');
				}
			}
			
			if(!isset($error))
			{
				$username   = mysql_real_escape_string($_POST['user']);
				$pass       = md5($_POST['password']);// MD5
				$name       = mysql_real_escape_string($_POST['name']);
				$secondname = mysql_real_escape_string($_POST['secondname']);
				$city		= mysql_real_escape_string($_POST['city']);
				$tipo		= mysql_real_escape_string($_POST['tipo']);
				
				//$ip         = mysql_real_escape_string($_SERVER['HTTP_HOST']);
	
				$query = mysql_query("INSERT INTO `users` (`idUser`, `username`, `password`, `name`, `secondname`, `city`, `tipo`, `dataRegistrazione`) VALUES (NULL, '$username', '$pass', '$name', '$secondname', '$city', '$tipo', now())") or die(mysql_error());
				if($query)
				{
					print "<br/><div><span>Complimenti hai creato l'account!<br/><span>Ora puoi fare <a href=\"login.php\">il login</a>.</span><br/></div>";
					include_once('login.php');
				}
				else
				{
					
					print "<div><span>Errore!</span></div>";
	
				}
			}
		}
	}
	else
	{
		print "sorry.. :'(";
	}
?>

     <script type="text/javascript" src="script/jquery-1.8.3.js"></script>
     <script src="bootstrap/js/bootstrap.min.js"></script>
     <script src="script/registration.js"></script>
</body>
</html>