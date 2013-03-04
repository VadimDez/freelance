<?php
    if(isset($_POST['login']))
    {
        include('dbConnect.php');
        if(!isset($_POST['login']))
        {
	        include('template/loginForm.html');
        }
        else
        {
	        if(isset($_POST['user'])&&$_POST['user']==""||!isset($_POST['user']))
	        {
	        $error[] = "Campo login non deve essere vuoto.";
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
			        print "<div class=\"error\">";
			        foreach ($error as $ers)
			        {
                        print "<span>".$ers."</span><br/>";
			        }
			        print "</div>";
			        include('template/loginForm.html');
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
                
                    // setto Coockie
                    setcookie("idUser",$id,time()+364*24*60*60,"/"); //per 364 giorni
                
                
			        header("Location: index.php");
		        }
		        else
                {
                    // se utente ha inserito la password sbagliata
		            echo '</br><div class="error"><span>La password inserita ? sbagliata.</span><br/></div>';
                    include('template/loginForm.html');
		        }
	        }
        }
    }
    else
    {
        include('template/loginForm.html');
    }
?>