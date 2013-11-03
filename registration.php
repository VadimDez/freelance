<!DOCTYPE HTML>
<html lang="it">
	<head>
    	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Registrazione</title>
        <?php include('include.php'); ?>
</head>
<body >

<?php
	if(!@include('check.php'))
	{
		@include_once('template/header.html');
		include_once('dbConnect.php');
		if(!isset($_POST['register']))
		{
			include_once('template/registrationForm.html');
			include_once('template/footer.html');
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

            if(!isset($_POST['checkbox']) && $_POST['checkbox'] == false)
            {
                $error[] = "Devi essere d'accordo con il nostro regolamento";
            }

            if(!isset($_POST['email']) && strlen($_POST['email']) > 0)
            {
                $error[] = "Inserisci la tua e-mail.";
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
                    $printError = '<div class="alert alert-error">
                        <a class="close" data-dismiss="alert" href="#">×</a>';
                    foreach ($error as $ers)
                    {
                        $printError .= "<span>".$ers."</span><br/>";
                    }
                    $printError .= '</div>';
					include_once('template/registrationForm.html');
					include_once('template/footer.html');
				}
			}
			
			if(!isset($error))
			{
                include_once('model.php');
                $parser = new parser();
                $username = $parser->textParsing($_POST['user']);
				$pass       = md5($_POST['password']);// MD5
				$name       = $parser->textParsing($_POST['name']);
				$secondname = $parser->textParsing($_POST['secondname']);
				$city		= $parser->textParsing($_POST['city']);
				$tipo		= mysql_real_escape_string($_POST['tipo']);
				
				//$ip         = mysql_real_escape_string($_SERVER['HTTP_HOST']);
	
				$query = mysql_query("INSERT INTO `users` (`idUser`, `username`, `password`, `name`, `secondname`, `city`, `tipo`, `dataRegistrazione`) VALUES (NULL, '$username', '$pass', '$name', '$secondname', '$city', '$tipo', now())") or die(mysql_error());
				if($query)
				{
					print '<br/><div class="alert alert-success"><span>Complimenti hai creato l\'account!<br/></div>';
					include_once('login.php');
				}
				else
				{
                    $printError = '<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>';
                    $printError .= "<span>Errore!</span><br/>";
                    $printError .= '</div>';
				}
			}
		}
	}
	else
	{
		print "sorry.. :'(";
	}
?>

     <script src="script/registration.js"></script>
</body>
</html>