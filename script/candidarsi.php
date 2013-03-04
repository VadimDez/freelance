<?php
include_once('../dbConnect.php');
session_start();
include_once('../pick.php');
// per cancellare post
if(isset($_POST['data']) && isset($_POST['type']) && isset($_POST['user']))
{
    $idPost = $_POST['data'];
	$idUser = $_POST['user'];
    $type = '';
    if($_POST['type'] == 'join')
    {
        $type = 'likes';
		echo json_encode(true);
    }
    else
    {
        if($_POST['type'] == 'exit')
        {
            $type = 'dislikes';
			echo json_encode(true);
        }
    }
    /*
	if(mysql_num_rows(mysql_query("SELECT * FROM $type WHERE idUser='$id' AND idThing='$idPost'")) > 0)
	{
    // se ha gia messo dislike
        mysql_query("DELETE FROM $type WHERE idUser='$id' AND idThing='$idPost'") or die(mysql_error());
        
	}
	else
	{
        // se non ha ancora messo dislike
        mysql_query("INSERT INTO $type (id, idUser, idThing) VALUES(NULL, '$id', '$idPost')") or die(mysql_error());
	}
	$risultato = mysql_num_rows(mysql_query("SELECT * FROM $type WHERE idThing='$idPost'"));
    mysql_query("UPDATE posts SET $type='$risultato' WHERE idPost='$idPost'") or die(mysql_error());
    */
    
}

?>