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
		$query = mysql_query("SELECT * FROM candidati WHERE idUser='$idUser'") or die(mysql_error());
		if(mysql_num_rows($query) == 0)
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
					$risultato .= '<tr><td><a href="project.php?id=' . $idProj . '">' . $infoProj['nomeProj'] . '</a></td></tr>';
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
    <table class="table table-striped table-hover">
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

