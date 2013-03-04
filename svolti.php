<?php
	session_start();
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
	
	include('dbConnect.php');
	if($idUser && !isset($_POST['action']))
	{
		
		$query = mysql_query("SELECT * FROM vincenti,project WHERE vincenti.idProj=project.idProj AND vincenti.idUser='$idUser' ORDER BY project.dataProj ASC") or die(mysql_error());
		if(mysql_num_rows($query) == 0)
		{
			$risultato = "Non ci sono progetti";	
		}
		else
		{
			while($infoProj = mysql_fetch_assoc($query))
			{
					$idProj 	= $infoProj['idProj'];
					
					if($infoProj['delivered'] == 0)
					{
						// se e' da confermare
						$style = ' class="warning"';
					}
					
					$risultato .= '<tr' . $style . '><td><a href="project.php?id=' . $idProj . '">' . $infoProj['nomeProj'] . '</a></td>';
					if($infoProj['delivered'] == 0 && ($_SESSION['idUser'] == $idUser))
					{
						$risultato	 .= '<td>
											<form action="consegna.php" method="post">
												<input type="hidden" name="proj" value="' . $idProj . '"/>
												<input type="submit" name="consegna" value="Consegna"/>
											</form>
										</td>';
					}
					$risultato .= '</tr>';
			}
		}
	}
	else
	{
		header('location:index.php');
		exit();
	}
	
	
?>


<div>
    <table class="table">
      <thead>
        <tr>
          <th>Titolo</th>
        </tr>
      </thead>
      <tbody>
        <?php print $risultato; ?>
      </tbody>
    </table>
</div>