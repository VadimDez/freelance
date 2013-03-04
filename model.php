<?php
session_start();
// gestione database
class myConnection
{
    public function connect()
    {
        $mysql_hostname = "localhost"; // name mysql host
        $mysql_user		= "root"; // user mysql
        $mysql_password = ""; // pass
        $mysql_database = "freelance"; //mysql db
        $db				= mysql_connect($mysql_hostname, $mysql_user, $mysql_password) or die("Errore di connessione con la database");
        $select 		= mysql_select_db($mysql_database, $db) or die("Errore della selezione della databese");
        if($db && $select)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function close()
    {
        mysql_close();
    }
}

// cerca
class ModelSearchCategory
{
    function searchCategory($category,$page = 1)
    {
        $risultato = '';
        $page = 20*$page;
        $query = mysql_query("SELECT * FROM project, categorie WHERE idCategoria='$category' AND idCat='$category' ORDER BY dataProj DESC LIMIT $page") or die(mysql_error());
        if(mysql_num_rows($query) > 0)
        {
            while($row = mysql_fetch_assoc($query))
            {
                $text = $row['descrizione'];
                $text = substr($text,0,350);
                $risultato .= '<blockquote><div>
					<div>
						<a href="project.php?id=' . $row['idProj'] . '"><p>' . $row['nomeProj'] .'</p></a>
					</div>
					<div>
							<small><a href="search.php?cat=' . $row['idCat'] . '">' . $row['nomeCat'] . '</a></small>
					</div>
					<div>
						<pre style="float:right;">' . $row['prezzo'] . '</pre>' . $text . '
					</div>
					<div>
						<small>' . $row['dataProj'] . '</small>
					</div>
				</div></blockquote><br/>';
            }
        }
        else
        {
            $risultato = 'Nessun annuncio.';
        }
        /*$cat = new home;
        $categorie = $cat->categorie();
        include('template/category_view.html');*/
        $this->loadViewSearch($risultato);
    }

    function loadViewSearch($ris)
    {
        $risultato = $ris;
        $cat = new home;
        $categorie = $cat->categorie();
        include('template/category_view.html');
    }

    function searchByWord($word)
    {
        $projects ='';
        $query = mysql_query("SELECT * FROM project, categorie WHERE project.categoria = categorie.idCat AND ( project.nomeProj LIKE '%$word%' OR project.descrizione LIKE '%$word%' OR project.richieste LIKE '%$word%' ) ORDER BY project.dataProj DESC") or die(mysql_error());
        if(mysql_num_rows($query) > 0)
        {
            while($row = mysql_fetch_assoc($query))
            {
                $text = $row['descrizione'];
                $text = substr($text,0,350);
                $projects .=
                    '<blockquote><div>
							<div>
								<a href="project.php?id=' . $row['idProj'] . '"><p>' . $row['nomeProj'] .'</p></a>
							</div>
							<div>
								<small><a href="search.php?cat=' . $row['idCat'] . '">' . $row['nomeCat'] . '</a></small>
							</div>
							<div>
								<pre style="float:right;">' . $row['prezzo'] . '</pre>' . $text . '
							</div>
							<div>
								<small>' . $row['dataProj'] . '</small>
							</div>
						</div></blockquote><br/>';
            }
        }
        else
        {
            $projects = '0 risultati';
        }
        $this->loadViewSearch($projects);
    }

}

// la classe per la pagina iniziale
class home
{

    function categorie()
    {
        $categorie = '';
        $query = mysql_query("SELECT * FROM categorie") or die(mysql_error());
        while($row = mysql_fetch_assoc($query))
        {
            $categorie .= '<li><a href="search.php?cat=' . $row['idCat'] . '">' . $row['nomeCat'] . '</a></li>';
        }
        return $categorie;
    }

    function annunci($page = 1)
    {
        $projects ='';
        $page = ($page - 1 ) * 10;
        // stampo ogni annuncio
        $query = mysql_query("SELECT * FROM users, project, categorie WHERE users.idUser = project.idUser AND categorie.idCat = project.categoria ORDER BY dataProj DESC LIMIT $page, 10") or die(mysql_error());
        if(mysql_num_rows($query) == 0)
        {
            $projects ='0 progetti.';
        }
        else
        {
            while($row = mysql_fetch_assoc($query))
            {
                $text = $row['descrizione'];
                $text = substr($text,0,350);
                // formattazione per ogni annuncio
                $projects .=
                    '<blockquote><div>
							<div>
							<pre style="float:right;">' . $row['prezzo'] . '</pre>
								<a href="project.php?id=' . $row['idProj'] . '"><p>' . $row['nomeProj'] .'</p></a>

							</div>
							<div>
								<small><a href="search.php?cat=' . $row['idCat'] . '">' . $row['nomeCat'] . '</a></small>
							</div>
							<div>
								' . $text . '
							</div>
							<div>
								<small>' . $row['dataProj'] . '</small>
							</div>
						</div></blockquote><br/>';
            }
        }
        // fine stampa annuncio
        return $projects;
    }

    function pageCounter($page = 1)
    {
        // conto il numero degli annunci
        $query = mysql_query("SELECT COUNT(*) AS num FROM project") or die(mysql_error());
        $row = mysql_fetch_assoc($query);

        // ottengo intero
        $numPage = ($row['num'] / 10);
        if(!is_int($numPage))
        {
            $numPage += 1;
            $numPage = (int)$numPage;
        }

        if($page <= $numPage && $page >= 1)
        {
            // pages
            $pages = '<div class="pagination pagination-centered">
				  <ul>
					<li';
            if($page == 1)
            {
                $pages .= ' class="disabled"';
            }
            $pages .= '><a href="index.php">&laquo;</a></li>';
            if($page >= 3)
            {
                for($c = $page-2;$c < $page; $c++)
                {
                    $pages .= '<li><a href="index.php?page=' . $c . '">' . $c . '</a></li>';
                }
            }
            else
            {
                if($page >= 2)
                {
                    $numero = $page-1;
                    $pages .= '<li><a href="index.php?page=' . $numero . '">' . $numero . '</a></li>';
                }
            }
            $pages .= '<li class="active"><a href="#">' . $page . '</a></li>';

            if($page <= $numPage-2)
            {
                for($c = $page+1;$c < $page+3; $c++)
                {
                    $pages .= '<li><a href="index.php?page=' . $c . '">' . $c . '</a></li>';
                }
            }
            else
            {
                if($page <= $numPage-1)
                {
                    $numero = $page+1;
                    $pages .= '<li><a href="index.php?page=' . $numero . '">' . $numero . '</a></li>';
                }
            }
            $pages .= '<li';
            if($page == $numPage)
            {
                $pages .= ' class="disabled"';
            }
            $pages .= '><a href="index.php?page=' . $numPage . '">&raquo;</a></li>
				  </ul>
				</div>';
        }
        else
        {
            $pages = 'Pagina numero <strong>' . $page . '</strong> non esiste';
        }
        return $pages;
    }

}

class consegna
{
    function uploadFile($idProj,$dirFile)
    {
        $conn = new myConnection;
        $conn->connect();
        mysql_query("UPDATE project SET file='$dirFile' WHERE idProj='$idProj'") or die(mysql_error());
        mysql_query("UPDATE project SET delivered='1' WHERE idProj='$idProj'") or die(mysql_error());
        $conn->close();
    }
}


class ready
{
    function existFeedBack($idProj)
    {
        if(mysql_num_rows(mysql_query("SELECT idFeedBack FROM feedback WHERE idProj='$idProj'")) == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function insertFeed($idProj, $feedValue, $feedText)
    {
        $idUser 	= $_SESSION['idUser'];
        $query = mysql_query("SELECT * FROM project WHERE idProj='$idProj'") or die(mysql_error());
        if(mysql_num_rows($query) == 1)
        {
            $row = mysql_fetch_assoc($query);
            $idOwner = $row['idUser'];
            if($idOwner == $idUser)
            {
                $query = mysql_query("SELECT idUser FROM vincenti WHERE idProj='$idProj'") or die(mysql_error());
                if(mysql_num_rows($query) == 1)
                {
                    $row = mysql_fetch_assoc($query);
                    $idWinner = $row['idUser'];
                    mysql_query("INSERT INTO feedback(idFeedBack, idReceiver, idSender, value, text, idProj, dataFeed) VALUES(NULL, '$idWinner', '$idOwner', '$feedValue', '$feedText', '$idProj', now() )") or die(mysql_error());

                    if($feedValue == '+')
                    {
                        //$val = "feedPos";
                        $feedNum = mysql_query("SELECT COUNT(*) AS num FROM feedback WHERE idReceiver='$idWinner' AND value='+'") or die(mysql_error());
                        $feedNum = mysql_fetch_assoc($feedNum);
                        $feedNum = $feedNum['num'];
                        mysql_query("UPDATE users SET feedPos='$feedNum' WHERE idUser='$idWinner'") or die(mysql_error());
                    }
                    else
                    {
                        //$val = "feedNeg";
                        $feedNum = mysql_query("SELECT COUNT(*) AS num FROM feedback WHERE idReceiver='$idWinner' AND value='-'") or die(mysql_error());
                        $feedNum = mysql_fetch_assoc($feedNum);
                        $feedNum = $feedNum['num'];
                        mysql_query("UPDATE users SET feedNeg='$feedNum' WHERE idUser='$idWinner'") or die(mysql_error());
                    }
                    /*$feedNum = mysql_query("SELECT '$val' FROM users WHERE idUser='$idWinner'") or die(mysql_error());
                    $feedNum = mysql_fetch_assoc($feedNum);
                    $feedNum = $feedNum[$val];
                    $feedNum += 1;
                    mysql_query("UPDATE users SET '$val'='$feedNum' WHERE idUser='$idWinner'") or die(mysql_error());*/
                }
            }
        }
    }
}


class users
{
    function showUsers($t = "NULL", $page = 1)
    {
        $start = ($page - 1) * 20;
        $conn = new myConnection();
        $conn->connect();

        if($t != "NULL")
        {
            $query = mysql_query("SELECT * FROM users WHERE tipo='$t' ORDER BY secondname DESC LIMIT $start, 20");

        }
        else
        {
            $query = mysql_query("SELECT * FROM users ORDER BY secondname DESC LIMIT $start, 20");
        }



        //lista per gli utenti
        $users ='';

        while($row = mysql_fetch_assoc($query))
        {
            $userId     = $row['idUser'];
            $name       = $row['name'];
            $secondname = $row['secondname'];
            $img        = $row['img'];
            $city       = $row['city'];
            $tipo        = $row['tipo'];

            if($tipo == 1)
            {
                $tipo = "Azienda";
            }
            else
            {
                $tipo = "Privato";
            }


            $users .= '<table><tr><td><img src="' .  $img  . '" width="200"/></td><td valign="top"><a href="profile.php?id=' . $userId . '">' . $name . ' ' . $secondname . '</a><br/>' . $tipo . '<br/>' . $city . '</td></tr></table><hr/>';
        }
        $conn->close();
        return $users;
    }

    function pageCounter($page = 1, $t = "NULL")
    {
        $conn = new myConnection();
        $conn->connect();
        // conto il numero degli annunci

        if($t == 1 || $t == "0")
        {
            $query = mysql_query("SELECT COUNT(*) AS num FROM users WHERE tipo='$t'") or die(mysql_error());
            $tipo = '&t=' . $t;
        }
        else
        {
            $query = mysql_query("SELECT COUNT(*) AS num FROM users") or die(mysql_error());
            $tipo = '';
        }

        $row = mysql_fetch_assoc($query);

        // ottengo intero
        $numPage = ($row['num'] / 20);
        if(!is_int($numPage))
        {
            $numPage += 1;
            $numPage = (int)$numPage;
        }



        if($page <= $numPage && $page >= 1)
        {
            // pages
            $pages = '<div class="pagination pagination-centered">
				  <ul>
					<li';
            if($page == 1)
            {
                $pages .= ' class="disabled"';
            }
            $pages .= '><a href="users.php">&laquo;</a></li>';
            if($page >= 3)
            {
                for($c = $page-2;$c < $page; $c++)
                {
                    $pages .= '<li><a href="users.php?page=' . $c . $tipo . '">' . $c . '</a></li>';
                }
            }
            else
            {
                if($page >= 2)
                {
                    $numero = $page-1;
                    $pages .= '<li><a href="users.php?page=' . $numero . $tipo . '">' . $numero . '</a></li>';
                }
            }
            $pages .= '<li class="active"><a href="users.php">' . $page . '</a></li>';

            if($page <= $numPage-2)
            {
                for($c = $page+1;$c < $page+3; $c++)
                {
                    $pages .= '<li><a href="users.php?page=' . $c . $tipo . '">' . $c . '</a></li>';
                }
            }
            else
            {
                if($page <= $numPage-1)
                {
                    $numero = $page+1;
                    $pages .= '<li><a href="users.php?page=' . $numero . $tipo . '">' . $numero . '</a></li>';
                }
            }
            $pages .= '<li';
            if($page == $numPage)
            {
                $pages .= ' class="disabled"';
            }
            $pages .= '><a href="users.php?page=' . $numPage . $tipo . '">&raquo;</a></li>
				  </ul>
				</div>';
        }
        else
        {
            $pages = 'Pagina numero <strong>' . $page . '</strong> non esiste';
        }

        $conn->close();

        return $pages;
    }

}

// classe per la gestine dei messaggi
class messagges
{
    function counterUnread($idUser)
    {
        $conn = new myConnection();
        $conn->connect();
        $num = mysql_query("SELECT COUNT(*) as num FROM messaggi WHERE idReceiver='$idUser' AND `read`='0'") or die(mysql_error());
        $num = mysql_fetch_assoc($num) or die(mysql_error());
        $num = $num['num'];
        $conn->close();
        if($num > 0)
        {
            $num = '<span class="label label-important">' . $num . '</span>';
            return $num;
        }
    }
}

class myHeader
{
    function left()
    {
        if(include('check.php'))
        {
            // se utente ha fatto il login
            $msg = new messagges();
            $idUser = $_SESSION['idUser'];
            $msgNum = $msg->counterUnread($idUser);

            $menu = '<li><a href="profile.php">Profile</a></li>
                 <li><a href="mail.php">Messagi<?php print $msgNum; ?></a></li>
                 <li><a href="logout.php">Logout</a></li>';
        }
        else
        {

            $menu = '<li><a href="registration.php">Registrazione</a></li>
                     <li><a href="login.php">Login</a></li>';

        }
        return $menu;
    }

}
?>