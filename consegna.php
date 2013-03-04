<?php
	$idProj = $_POST['proj'];
	$idUser = $_SESSION['idUser'];

	if(isset($_POST['consegna']))
	{
		include('template/consegna.html');
	}
	else
	{
		if($_POST['caricamento'])
		{
			if(!$_FILES['fileField']['tmp_name'])
			{
				$msg = '<font color="red">Prima devi selezionare l\'immagine.</font>';
			}
			else
			{
				$maxsize = 100000000; // Utente puo' scegliere al massimo l'immagine che e' minore di 100MB
				if($_FILES['fileField']['size'] > $maxsize)
				{
					$msg = '<font color=\"red\">Puo\' caricare solo file che ha grandezza minore di 100MB.</font>';
					unlink($_FILES['fileField']['tmp_name']);
				}
				else
				{
					if(!preg_match("/\.(rar|zip)$/i",$_FILES['fileField']['name']))
					{
						$msg = '<font color="red">Puo\' caricare solo file di tipo RAR o ZIP.</font>';
						unlink($_FILES['fileField']['tmp_name']);
					}
					else
					{
						$dirFile = "projectFiles/$idProj.rar";
						$newfile = move_uploaded_file($_FILES['fileField']['tmp_name'],$dirFile);
						//$msg = '<font color="green">L\'immagine del profile e\' modificata.</font>';
						// query per caricare indirizzo dell'immagine nella database
						//$query = mysql_query("UPDATE users SET img='$dirFile' WHERE idUser='$idUser'") or die(mysql_error());
						include('model.php');
						$consegna = new consegna;
						$consegna->uploadFile($idProj,$dirFile);
					}
				}
			}
		}
	}
	
	if($msg)
	{
		include('template/consegna.html');	
	}
	else
	{
		if(!isset($_POST['consegna']) && !isset($_POST['caricamento']))
		{
			header('location:profile.php');
		}
	}
	
?>