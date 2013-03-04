<?php
session_start();
unset($_SESSION['idUser']);
setcookie("idUser","",time() - 3600);
header("Location: index.php");//redirecting alla pagina iniziale
?>