<?php

	if(isset($_GET['id']))
	{
		  $idUser = $_GET['id'];
	}
	else
	{
		if(isset($_SESSION['idUser']))
		{
			$idUser = $_SESSION['idUser'];
		}
		else
		{
			header('location:index.php');	
			exit();
		}
	}
		
	if($idUser)
	{
		include('dbConnect.php');
		$query = mysql_query("SELECT * FROM project WHERE idUser='$idUser' ORDER BY dataProj DESC") or die(mysql_error());
		
		$count = mysql_num_rows($query);
		if($count == 0)
		{
			$risultato = "Non ci sono progetti";
		}
		else
		{
			while($row = mysql_fetch_assoc($query))
			{
					$idProj = $row['idProj'];
					$query2 = mysql_query("SELECT * FROM project WHERE idProj='$idProj'") or die(mysql_error());
					$infoProj = mysql_fetch_assoc($query2);
					
					
					if($infoProj['closed'] == 0)
					{
						$risultato .= '<tr class="success">';
						$numProposte = mysql_num_rows(mysql_query("SELECT * FROM candidati WHERE idProj='$idProj'"));
						$numProposte = '<span class="badge badge-info">' . $numProposte . '</span>';
					}
					else
					{
						if($infoProj['delivered'] == 0 && $infoProj['closed'] == 1)
						{
							$risultato .= '<tr class="warning">';
						}
						else
						{
							$risultato .= '<tr>';
						}
					}
					$risultato .= '<td><a href="project.php?id=' . $idProj . '">' . $infoProj['nomeProj'] . ' ' . $numProposte . '</a></td>
									  <td>';
					if($infoProj['closed'] == 0)
					{
						$risultato .= 'Aperto';
					}
					else
					{
						if($infoProj['delivered'] == 0 && $infoProj['closed'] == 1)
						{
							$risultato .= 'Consegna';	
						}
						else
						{
							$risultato .= 'Chiuso';	
						}
					}
						$risultato .= '</td>
									  <td>' . $infoProj['prezzo'] . '</td>
									</tr>';
			}
		}
	}
	else
	{
		header('location:index.php');	
		exit();
	}
	
	
?>
<div class="row">
    <div class="span11">
        <?php
        if($count == 0)
        {
            print $risultato;
        }
        else
        {
            ?>
            <table class="table">
                <thead>
                <tr>
                    <th>Titolo</th>
                    <th>Stato</th>
                    <th>Prezzo</th>
                </tr>
                </thead>
                <tbody>
                    <?php print $risultato; ?>
                </tbody>
            </table>
            <?php
        }
        ?>
    </div>
</div>


