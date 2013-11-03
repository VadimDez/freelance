<?php
    session_start();
	$idProj = $_POST['proj'];
	$idUser = $_SESSION['idUser'];

    include('model.php');
    $consegna = new consegna;

	if(isset($_POST['consegna']))
	{
        $consegna->view("",$idProj);
	}
	else
	{
		if($_POST['caricamento'])
		{
			if(!$_FILES['fileField']['tmp_name'])
			{
				$msg = '<font color="red">Prima devi selezionare il file.</font>';
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
						if($newfile = move_uploaded_file($_FILES['fileField']['tmp_name'],$dirFile))
                        {
                            // query per caricare indirizzo dell'immagine nella database
                            $comment = $_POST['comment'];
                            $consegna->uploadFile($idProj,$dirFile,$comment);

                            $msg = '<font color="green">Progetto e\' stato caricato.</font>';
                        }
                        else
                        {
                            $msg = '<font color="red">Errore durante caricamento.</font>';
                        }
					}
				}
			}
		}
        if($_POST['pay'])
        {
            $consegna->paypal($_POST['pay'],$idProj);
        }
	}
	
	if($msg)
	{
        $consegna->view($msg,$idProj);
	}
	else
	{
		if(!isset($_POST['consegna']) && !isset($_POST['caricamento']))
		{
			header('location:profile.php');
            exit();
		}
	}
	
?>