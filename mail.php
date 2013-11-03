<?php
if(include('check.php'))
{
	
	include_once('pick.php');
    include_once('model.php');
	$query   = "";
	$msgForm = "";
	$list    = "";
	$sender = $_SESSION['idUser'];

	
	// controllo se utente ha premuto il tasto "Scrivi messaggio" dal profile
	if(isset($_POST['msg']))
	{
	
	// se utente vuole spedire messaggio
	
		if($_POST['msg'] == "msg" || ($_POST['msg'] == "sended"))
		{
			// query per ottentere le info del destinatario, cioe' il nome, cognome, la foto
			$query  = mysql_query("SELECT idUser, name, secondname, img FROM users WHERE idUser='$idUser'");
			
			// controllo se il risultato delle tuple e' maggiore di 0;
			if(mysql_num_rows($query) > 0)
			{
				// salvo informazioni sul utente che ricevera il messaggio
				while($row = mysql_fetch_assoc($query))
				{
					$idReceiver = $row['idUser'];
					$nameReceiver = $row['name'];
					$secondnameReceiver = $row['secondname'];
					$imageReceiver = $row['img'];
				}
				
				// se utente non ha ancora spedito il messaggio, ma ha solo cliccato sul bottono "scrivi messaggio"
				if($_POST['msg'] != "sended") 
				{
					$txt = "";
					// metto come messaggio letto
					if(isset($_POST['idMsg']))
					{
						$idMsg = $_POST['idMsg'];
						// aggiorno la db dicendo che utente ha letto il messaggio
						mysql_query("UPDATE `messaggi` SET `read`='1' WHERE `idMsg`='$idMsg' AND idReceiver='$sender'") or die (mysql_error());
						
						//
						if(isset($_POST['txtMsg']))
						{
							$txt = $_POST['txtMsg'];
						}
					}
					
					
					$msgForm = '<ul class="media-list">
					  <li class="media">
						<a class="pull-left" href="profile.php?id=' . $idReceiver . '">
						  <img class="media-object" src="' . $imageReceiver . '" width="100px" alt="">
						</a>
						<div class="media-body">
						  <h5 class="media-heading">Messaggio a:<a href="profile.php?id=' . $idReceiver . '">' . $nameReceiver . ' ' . $secondnameReceiver . '</a></h5>
						  	<form action="mail.php?id=' . $idReceiver . '" method="post">
								<div class="row-fluid">
									<div class="span11">
										' . $txt . '
									</div>
									<div class="span11">
										<textarea name="textToSend" rows="7" style="width:99%" placeholder="Messaggio..."></textarea>
										<input type="hidden" value="sended" name="msg" />
										<p class="text-right">
											<input type="submit" value="Scrivi" class="btn btn-inverse"/>
										</p>
									</div>
								</div>
							</form>
						</div>
					  </li>
					</ul>';
					
				}
				else
				{
					// se utente ha mandato il messaggio
					if($_POST['msg'] == "sended") 
					{
						$textTosend = $_POST['textToSend']; //testo da spedire
						// parsing del messaggio
						$textTosend = stripslashes($textTosend);
						$textTosend = strip_tags($textTosend);
						$textTosend = mysql_real_escape_string($textTosend);
						$textTosend = trim($textTosend, '\r\n');
						$textTosend = preg_replace('/\'/i','&#39', $textTosend);
						$textTosend = preg_replace('/`/i','', $textTosend);
						$textTosend = mysql_real_escape_string($textTosend);
						
						$query = mysql_query("INSERT INTO messaggi(idSender, idReceiver, time, text) VALUES('$sender', '$idUser', now(), '$textTosend')") or die (mysql_error());
						header("Location: mail.php"); //redirect
					}
				}
			}
		}
	}
	else
	{
	// se utente ha aperto link "Messaggi" deve vedere posta in arrivo:
		
		
		$style = ""; // variabile che dice se utente a selezionato messaggi o messaggi inviati
					 // 0 - posta in arrivo
					 // 1 - messaggi inviati
	
		if(isset($_POST['msgSend']))
		{
			// messaggi inviati
			$query = "SELECT * FROM messaggi WHERE idSender='$sender'";
			$query2= "SELECT * FROM users, messaggi WHERE users.idUser=messaggi.idReceiver and messaggi.idSender='$sender' ORDER BY time DESC";
			
			$style = "1";// 1 - messaggi inviati
		}
		else
		{
			// posta in arrivo
			$query = "SELECT * FROM messaggi WHERE idReceiver='$sender'";
			$query2= "SELECT * FROM users, messaggi WHERE users.idUser=messaggi.idSender and messaggi.idReceiver='$sender' ORDER BY time DESC";
			
			$style = "0";// 0 - posta in arrivo
		}
		$query = mysql_query($query)  or die (mysql_error());
		if(mysql_num_rows($query) > "0")
		{
			//lista per gli utenti
			$list ='';

            $parser = new parser();

			$query = mysql_query($query2)  or die (mysql_error());
			while($row = mysql_fetch_array($query))
			{
				$idReceiver       = $row['idUser'];
				$userName       = $row['name'];
				$userSecondname = $row['secondname'];
				$userImg        = $row['img'];
				$idMsg          = $row['idMsg'];
				$idSender       = $row['idSender'];
				$time           = $row['time'];
				$read           = $row['read'];
				//$time       = strftime("%H:%M:%S, %b, %d. %Y", strtotime($time)+("7:0:0, 0, 0, 0")); // per il server
				$time           = strftime("%H:%M:%S, %d %b %Y", strtotime($time));
				
				$text           = $row['text'];
				//parsing
                $text = $parser->textParsing($text);

				/*$text = stripslashes($text);
				$text = strip_tags($text);
				$text = mysql_real_escape_string($text);
				$text = trim($text, '\r\n');
				$text = preg_replace('/\'/i','&#39', $text);
				$text = preg_replace('/`/i','', $text);
				$text = mysql_real_escape_string($text);*/
				
				
				$idStyle = "well"; // lo stile per messaggi letti
				if($read == "0") // se messaggio non letto - cambia colore
				{
					$idStyle = "newMsg";
				}
				
				
				$idUser = ""; // id di utente che ha spedito o ha rivecuto,
							  // serve per la parte di messaggi in arrivo e messaggi mandati
				if($style == "1") // se ha aperto messaggi in arrivo
				{
					$idUser = $idReceiver; // deve vedere a chi a mandato il msg
				}
				else
				{
					$idUser = $idSender; // se e' posta in arrivo, devo vedere chi l'ha spedito
				}

                $list .=    '<div class="media ' . $idStyle . '">
                              <a class="pull-left" href="#">
                                <img class="media-object img-polaroid" src="' . $userImg . '" width="64" height="64" alt="">
                              </a>
                              <div class="media-body">
                                <h4 class="media-heading"><a href="profile.php?id=' .  $idUser  . '">' . $userName . ' ' . $userSecondname . '</a></h4>

                                <div>
                                ' . $text . '
                                    <hr/>
                                    <small style="float:left;">' . $time . '</small>
                                    <form class="pull-right" action="mail.php?id=' . $idUser . '" method="post">
                                    <input type="hidden" value="msg" name="msg" />
                                    <textarea name="txtMsg" style="display: none;">' . $text . '</textarea>
                                    <textarea name="idMsg" style="display: none;">' . $idMsg . '</textarea>
                                    <input type="submit" value="Rispondi" class="btn btn-inverse" ></form>
                                </div>
                              </div>
                            </div>';
			}
			$list .= '';
			
		}
		else
		{
			if($style == 0)
			{
				$list = "Non hai messaggi, mi dispiace.";
			}
			else
			{
				$list = "Non hai ancora mandato messaggi.";
			}
		}
	
	}
}
else
{
	header('location:index.php');
	exit();	
}

?>

<!DOCTYPE HTML>
<html>
    <head>
        <title>Messaggi</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <?php include('include.php'); ?>
        <link rel="stylesheet" type="text/css" href="style/main.css" >
        <link rel="stylesheet" type="text/css" href="style/msgs.css" >
    </head>

    <body>
    <div id="render">
        <?php include('template/header.html'); ?>
        <div class="show-of-head">
            <section class="content">
                <div class="container">
                    <div class="row">
                        <div class="span9 offset1 well">
                            <?php
                            if($msgForm != "")
                            {
                                print "$msgForm";
                            }
                            else
                            {
                                // bottoni posta in arr, posta spedita
                                ?>

                                <ul class="nav nav-tabs">
                                    <li>
                                        <form action="mail.php" method="post">
                                            <button type="submit" class="btn btn-inverse"><i class="icon-arrow-down icon-white"></i>Messaggi in arrivo</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="mail.php" method="post">
                                            <input type="hidden" value="msgSended" name="msgSend" id="msgSend" />
                                            <button type="submit" class="btn btn-inverse"><i class="icon-arrow-up icon-white"></i>Messaggi inviati</button>
                                        </form>
                                    </li>
                                </ul>

                                <?
                                //la lista dei messaggi;
                                print "$list";
                            }
                            ?>


                        </div>
                    </div>

                </div>
            </section>
        </div>
        <div heigh="10px">&#160;</div> <!-- lo spazio -->
    </div>
    </div>
    </div>
    <?php include('template/footer.html'); ?>
    </body>

</html>