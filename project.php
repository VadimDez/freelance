<?php
	session_start();

	include('dbConnect.php');
	include('model.php');
    $parser = new parser();

	// variabili
	$projects = '';
	
	
	if(isset($_GET['id']))
	{
		// se passa id del progetto
		$idProj = $_GET['id'];
		$idProj = $parser->textParsing($idProj);
		$query = mysql_query("SELECT * FROM project,users,categorie WHERE idProj = '$idProj' AND project.idUser = users.idUser AND categorie.idCat = project.idCategoria") or die(mysql_error());
		if(mysql_num_rows($query) == 0 )
		{
			$projects = '<div class="text-center">Il progetto é stato cancellato o non esiste.</div>';
            $notExist = true;
		}
		else
		{
			$row = mysql_fetch_assoc($query);
			$nameOwner 		 = $row['name'];
			$secondnameOwner = $row['secondname'];
			$idOwner  		 = $row['idUser'];
			$imgOwner		 = $row['img'];
			$tipoOwner		 = $row['tipo'];
			if($tipoOwner == 0)
			{
				$tipoOwner = "Privato";
			}
			else
			{
				$tipoOwner = "Azienda";	
			}

            // prendo prezzo ====================================================
            $prezzo1         = $row['prezzo'];
            $prezzo2         = $row['prezzo2'];
			$prezzo			 = $row['prezzo'];
            if($row['prezzo'] == 0 && $row['prezzo2'] == 0)
            {
                $prezzo = ' Da contrattare';
            }
            else
            {
                if($row['prezzo'] > 0 && $row['prezzo2'] == 0)
                {
                    $prezzo = $row['prezzo'];
                }
                else
                {
                    if($row['prezzo'] > 0 && $row['prezzo2'] > 0)
                    {
                        $prezzo = $row['prezzo'] . '-' . $row['prezzo2'];
                    }
                }
            }
            // ==================================================================
            // info sul prog.
			$nameProj 		 = $row['nomeProj'];
            $tipoProj        = $row['tipoProj'];
            $delivered       = $row['delivered'];
			$descProj		 = $row['descrizione'];
            $descProj        = $parser->textParsingWithNL($descProj);

			$richesteProj	 = $row['richieste'];
            $richesteProj    = $parser->textParsingWithNL($richesteProj);

            $timeProj        = strftime("%H:%M, %d %b %Y", strtotime($row['dataProj']));
            $categoria       = $row['nomeCat'];
            $idCat           = $row['idCat'];
            $pos             = $row['feedPos'];
            $neg             = $row['feedNeg'];

            if($tipoProj == 1)
            {
                $nameTipoProj = 'Freelance';
            }
            else
            {
                if($tipoProj == 2)
                {
                    $nameTipoProj = 'Sponsorizzazione';
                }
                else
                {
                    if($tipoProj == 3)
                    {
                        $nameTipoProj = 'Collaborazione';
                    }
                }
            }

			// controllo se progetto non e' chiuso ==================================================================================
			// closed = 0 - aperto
			// closed = 1 - chiuso perche' il proprietario ha selto la persona
			// closed = 2 - chiuso perche' il proprietario ha deciso di chiudere
			if($row['closed'] == 1)
			{
				$closed = true;	
			}
			else
			{
                if($row['closed'] == 2)
                {
                    $end = true;
                }
                else
                {
                    $end = false;
                }
				$closed = false;	
			}

            $me = $_SESSION['idUser'];
            if(mysql_num_rows(mysql_query("SELECT idUser FROM vincenti WHERE idUser='$me' AND idProj='$idProj'")) == 1)
            {
                $win = true;
            }
            else
            {
                $win = false;
            }
			
			// controllo per il tasto per candidarsi =================================================================================
			if(isset($_SESSION['idUser'])) // se ha gia fatto login
			{
				$idUser = $_SESSION['idUser'];
				// controllo se e' il proprietario
				$candidato = mysql_query("SELECT idUser FROM project WHERE idProj='$idProj' AND idUser='$idUser'") or die(mysql_error());
				if(mysql_num_rows($candidato) == 0)
				{
					$proprietario = false;
					// controllo se utente ha gia partecipato
					$candidato = mysql_query("SELECT idUser FROM candidati WHERE idProj='$idProj' AND idUser='$idUser'") or die(mysql_error());
					if(mysql_num_rows($candidato) == 0)
					{
						// non ha ancora partecipato - aggiungo bottone per candidarsi
						$joined = false;
					}
					else
					{
						// sta partecipando - aggiungo bottone "esci"
						$joined = true;
					}
				}
				else
				{
					$proprietario = true;
					
					// controllo se' utente puo' ritirare file del prog. e lasciare feedback
					if(mysql_num_rows(mysql_query("SELECT idUser FROM project WHERE idUser='$idUser' AND idProj='$idProj' AND ownerPaid='1' AND delivered='1' AND closed='1'")) == 1)
					{
						$finished = true;
					}

                    // controllo se utente puo' vedere tasto Ritira
                    if(mysql_num_rows(mysql_query("SELECT idUser FROM project WHERE idUser='$idUser' AND idProj='$idProj' AND ownerPaid='1' AND delivered='1' AND closed='1'")) != 1)
                    {
                        $showButton = 'hidden';
                    }
				}
			
			}
			
			$comments = '';
			$query = mysql_query("SELECT * FROM candidati, users WHERE idProj='$idProj' AND candidati.idUser = users.idUser ORDER BY data") or die(mysql_error());
			if(mysql_num_rows($query) == 0)
			{
				// in caso se non ci sono proposte
				$comments .= '<div class="well"><p class="text-center">Non ci sono proposte</p></div>';	
			}
			else
			{
					if($proprietario == true)
					{
						// controllo se proprietario ha gia' sceglto il candidato
						$controllo = mysql_query("SELECT idUser FROM vincenti WHERE idProj='$idProj'") or die(mysql_error());
						if(mysql_num_rows($controllo) == 1)
						{
							// se si:
							$decide = true;
							$check = mysql_query("SELECT idUser FROM project WHERE idProj='$idProj' AND ownerPaid='1'") or die(mysql_error());
							if(mysql_num_rows($check) == 1)
							{
								$pagato = true;	
							}
						}
						else
						{
							// se no:
							$decide = false;
						}
					}
				
				// stampo i commenti , cioe' proposte
				while($row = mysql_fetch_assoc($query))
				{
                    $time = strftime("%H:%M, %d %b %Y", strtotime($row['data']));
					$comments .= '<div class="media well">
						  <a class="pull-left" href="profile.php?id=' . $row['idUser'] .'">
							<img class="media-object" src="' . $row['img'] . '" width="64px">
						  </a>
						  <div class="media-body">';

                    if(!$decide && $proprietario)
                    {
                        //$comments .= '<input type="button" value="Scegli" id="take" data-in="' . $idProj . '"/>';
                        $comments .= '<div id="choose" class="media-heading pull-right">
												<a href="#scegli" role="button" class="btn btn-info" data-toggle="modal">Scegli</a>

												<div id="scegli" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
												  <div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
													<h3 id="myModalLabel">Scelta del candidato</h3>
												  </div>
												  <div class="modal-body" id="cont">
													<p>Sei sicuro di scegliere questo candidato per fare il tuo progetto?</p>';

                        if($tipoProj == 2)
                        {
                            // se sponsorizzazione

                            $comments .= '<span>Inserisci il tuo PayPal:</span>
                                            <input type="text" maxlength="60" min="6" name="pay" id="pay" />';

                        }

                        $comments .= '</div>
												  <div class="modal-footer">
													<button class="btn" data-dismiss="modal" aria-hidden="true">Chiudi</button>
													<button class="btn btn-info" data-dismiss="modal" aria-hidden="true" id="take" proj="' . $idProj . '" usr="' . $row['idUser'] . '">Scegli</button>
												  </div>
												</div>
									    </div>';
                    }
                            // il commento
                            $text = $row['comment'];
                            // parsing di commento
                            $text = $parser->textParsingWithNL($text);

							$comments .= '<h4 class="media-heading"><a href="profile.php?id=' . $row['idUser'] . '">' . $row['name'] . ' ' . $row['secondname'] . '</a></h4>
                            ' . $text . '
							<div class="media pull-right">
							  <small><i class="icon-calendar"></i> ' . $time . '</small>
							</div>';

                            // comments
                            $prop = $row['idProposta'];
                            $commentq= mysql_query("SELECT u.idUser, img,idProj,name,secondname,data,comment FROM comments as c, users as u WHERE u.idUser=c.idUser AND c.idProposta='$prop'") or die(mysql_error());
                            if(mysql_num_rows($commentq) > 0)
                            {
                                $comment = new comments();
                                while($pull = mysql_fetch_assoc($commentq))
                                {
                                    $comments .= '<hr/>';
                                    $comments .= $comment->comment($pull);
                                }
                            }
                            // fine comments

						    $comments .= '</div>
						                </div>';
                        if($proprietario || ($row['idUser'] == $_SESSION['idUser'] && $closed == false))
                        {
                            $comments .= '<div class="row">
                                            <div class="comment_form span7 well well-small offset4" id="comment">
                                                <textarea style="width: 98%" id="text" placeholder="Commenta..."></textarea>
                                                <button type="button" id="send" class="btn btn-inverse pull-right" proj="' . $idProj . '" prop="' . $row['idProposta'] . '">Commenta</button>
                                            </div>
                                        </div>';
                        }
				}
			}
			
		}
	}
	else
	{
		$query = mysql_query("SELECT * FROM project ORDER BY dataProj DESC") or die(mysql_error());
		if(mysql_num_rows($query) == 0 )
		{
			$projects = "non ci sono progetti da fare";
		}
		else
		{
			while($row = mysql_fetch_assoc($query))
			{
				// stampo la lista dei progetti
				$projects .= '<div><a href="project.php?id=' . $row['idProj'] . '">' . $row['nomeProj'] . '</a></div> <br/>';
			}
		}
	}
	mysql_close();
	if(!isset($_GET['id']))
	{
		header('location:index.php');
		exit();
	}
?>

<!DOCTYPE HTML>
<html lang="it">
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>
        <?php if($nameProj)
            {
                print $nameProj;
            }
            else
            {
                print "Progetti";
		}?> - ifreelancer.it
		</title>

        <?php include('include.php'); ?>
        <script type="text/javascript" src="script/text.js"></script>
</head>
    <body onload="init()">
    <?php
    include('template/header.html');
    ?>
    <div class="show-of-head">
        <?php include('template/project.html'); ?>
    </div>
    <?php include('template/footer.html');

    // controllo del prog. se e' da sponsorizzare o no

    if($tipoProj == 2 && $proprietario)
    {
        ?>
    <script type="text/javascript" src="script/spons.js"></script>
        <?php
    }
    else
    {
        ?>

        <script type="text/javascript" src="script/inProject.js"></script>
        <?php
    }
    if($proprietario && ($tipoProj != 2))
    {
        ?>
    <script type="text/javascript" src="script/take.js"></script>
        <?php
    }
    ?>
    <script type="text/javascript" src="script/comment.js"></script>
    </body>

</html>