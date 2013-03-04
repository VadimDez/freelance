<?php

$mysql_hostname = "localhost"; // name mysql host
$mysql_user		= "root"; // user mysql
$mysql_password = ""; // pass
$mysql_database = "freelance"; //mysql db
$db				= mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Errore di connessione con la database");
mysql_select_db($mysql_database, $db) or die("Errore della selezione della databese");

?>