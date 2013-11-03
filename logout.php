<?php
session_start();

//setcookie("idC","",time() - 364*24*60*60);
setcookie("PHPSESSID","",time() - 364*24*60*60);
include_once('model.php');
$conn = new myConnection();
$conn->connect();
$id = $_SESSION['idUser'];
mysql_query("DELETE FROM session WHERE idUser='$id'") or die(mysql_error());
unset($_SESSION['idUser']);
$conn->close();
session_destroy();
header("Location: index.php");//redirecting alla pagina iniziale

?>