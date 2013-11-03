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
        @mysql_close();
    }
}

// cerca
class ModelSearchCategory
{
    function searchCategory($category,$page = 1)
    {
        $risultato = '';
        $pageStart = ($page-1)*10;
        $query = mysql_query("SELECT * FROM project, categorie WHERE categorie.idCat='$category' AND categorie.idCat=project.categoria ORDER BY dataProj DESC LIMIT $pageStart, 10") or die(mysql_error());
        if(mysql_num_rows($query) > 0)
        {
            while($row = mysql_fetch_assoc($query))
            {
                $risultato .= $this->viewAnnuncio($row);
            }
        }
        else
        {
            $risultato = 'Nessun annuncio.';
        }

        $pageCounter = $this->pageCounter($page,$category,2);
        $this->loadViewSearch($risultato,$pageCounter);
    }

    function loadViewSearch($ris,$pageCounter)
    {
        $risultato = $ris;
        $cat = new home;
        $categorie = $cat->categorie();
        $pages = $pageCounter;
        include('template/category_view.html');
    }

    function searchByWord($page = 1,$word)
    {
        $projects ='';
        $pageStart = ($page - 1 ) * 10;
        $query = mysql_query("SELECT * FROM users,project, categorie WHERE users.idUser = project.idUser AND project.categoria = categorie.idCat AND ( project.nomeProj LIKE '%$word%' OR project.descrizione LIKE '%$word%' OR project.richieste LIKE '%$word%' ) ORDER BY project.dataProj DESC LIMIT $pageStart, 10") or die(mysql_error());
        if(mysql_num_rows($query) > 0)
        {
            while($row = mysql_fetch_assoc($query))
            {
                // formattazione per ogni annuncio
                $projects .= $this->viewAnnuncio($row);
            }
        }
        else
        {
            $projects = '0 risultati';
        }
        $pageCounter = $this->pageCounter($page,$word,1);
        $this->loadViewSearch($projects,$pageCounter);
    }

    function pageCounter($page = 1,$word, $tipo)
    {
        // $tipo = 1 ricerca per parola
        // $tipo = 2 ricerca per categorie

        // conto il numero degli annunci
        if($tipo == 1)
        {
            $query = mysql_query("SELECT COUNT(idProj) AS num FROM project WHERE nomeProj LIKE '%$word%' OR descrizione LIKE '%$word%' OR richieste LIKE '%$word%'") or die(mysql_error());
        }
        else
        {
            $query = mysql_query("SELECT COUNT(idCat) AS num FROM project, categorie WHERE categorie.idCat='$word' AND categorie.idCat=project.categoria") or die(mysql_error());
            $tipo = 'cat=' . $word;
        }

        $row = mysql_fetch_assoc($query);

        // ottengo intero
        $numPage = ($row['num'] / 10);
        if(!is_int($numPage))
        {
            $numPage += 1;
            $numPage = (int)$numPage;
        }

        // pagination
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
            $pages .= '><a href="search.php?' . $tipo . '">&laquo;</a></li>';
            if($page >= 3)
            {
                for($c = $page-2;$c < $page; $c++)
                {
                    $pages .= '<li><a href="search.php?' . $tipo . '&page=' . $c . '">' . $c . '</a></li>';
                }
            }
            else
            {
                if($page >= 2)
                {
                    $numero = $page-1;
                    $pages .= '<li><a href="search.php?' . $tipo . '&page=' . $numero . '">' . $numero . '</a></li>';
                }
            }
            $pages .= '<li class="active"><a href="#">' . $page . '</a></li>';

            if($page <= $numPage-2)
            {
                for($c = $page+1;$c < $page+3; $c++)
                {
                    $pages .= '<li><a href="search.php?' . $tipo . '&page=' . $c . '">' . $c . '</a></li>';
                }
            }
            else
            {
                if($page <= $numPage-1)
                {
                    $numero = $page+1;
                    $pages .= '<li><a href="search.php?' . $tipo . '&page=' . $numero . '">' . $numero . '</a></li>';
                }
            }
            $pages .= '<li';
            if($page == $numPage)
            {
                $pages .= ' class="disabled"';
            }
            $pages .= '><a href="search.php?' . $tipo . '&page=' . $numPage . '">&raquo;</a></li>
				  </ul>
				</div>';
        }
        else
        {
            if($page > 1)
            {
                $pages = 'Pagina numero <strong>' . $page . '</strong> non esiste';

            }
        }
        return $pages;
    }

    function viewAnnuncio($row)
    {
        // la vista per annuncio
        $idProj = $row['idProj'];
        $numProposte = mysql_num_rows(mysql_query("SELECT idProj FROM candidati WHERE idProj='$idProj'"));
        $time = strftime("%H:%M, %d %b %Y", strtotime($row['dataProj']));

        $text = $row['descrizione'];
        $text = substr($text,0,350);

        $parser = new parser();
        $text = $parser->textParsing($text);

        switch($row['tipoProj'])
        {
            case 1:$tipo = 'Freelance';
                break;
            case 2:$tipo = 'Sponsor';
                break;
            case 3:$tipo = 'Collaborazione';
                break;
        }

        $project = '<div class="span8 banner">
                        <blockquote>
                            <div>
                                <div>';
                            if($row['tipoProj'] < 3)
                            {
                                $project .= '<pre style="float:right;">' . $row['prezzo'] . '</pre>';
                            }
                            $project .= '<a href="project.php?id=' . $row['idProj'] . '"><p><strong>' . $row['nomeProj'] .'</strong></p></a>

                                </div>
                                <div>
                                    <small class="tipo"><a href="search.php?cat=' . $row['idCat'] . '">' . $row['nomeCat'] . '</a> - ' . $tipo . '</small>
                                </div>
                                <div class="text">
                                    ' . $text . '
                                </div>
                                <div>
                                    <br/>
                                    <small class="tipo"><i class="icon-calendar"></i>' . $time . ' | <a href="profile.php?id=' . $row['idUser'] . '"><i class="icon-user"></i>' . $row['name'] . ' ' . $row['secondname'] . '</a> | <i class="icon-comment"></i> ' . $numProposte . ' Proposte</small>
                                </div>
						    </div>
						</blockquote>
				    </div><br/>';
        return $project;
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
                $idProj = $row['idProj'];
                $numProposte = mysql_num_rows(mysql_query("SELECT idProj FROM candidati WHERE idProj='$idProj'"));
                $time = strftime("%H:%M, %d %b %Y", strtotime($row['dataProj']));

                $text = $row['descrizione'];
                $text = substr($text,0,350);

                $parser = new parser();
                $text = $parser->textParsing($text);

                switch($row['tipoProj'])
                {
                    case 1:$tipo = 'Freelance';
                        break;
                    case 2:$tipo = 'Sponsor';
                        break;
                    case 3:$tipo = 'Collaborazione';
                        break;
                }

                // controllo prezzo
                //se e' nullo - quindi metto a scelta
                //se il prezzo e' un range - stampo un range
                $prezzo = $row['prezzo'];
                if($row['prezzo'] == 0 && $row['prezzo2'] == 0)
                {
                    $prezzo = 'Da contrattare';
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

                // formattazione per ogni annuncio
                $projects .=
                    '<div class="span8 banner">
                        <blockquote>
                            <div>
                                <div>';

                                if($row['tipoProj'] < 3)
                                {
                                     $projects .= '<pre style="float:right;">' . $prezzo . '</pre>';
                                }
                                $projects .= '<a href="project.php?id=' . $row['idProj'] . '"><p><strong>' . $row['nomeProj'] .'</strong></p></a>

                                </div>
                                <div>
                                    <small class="tipo"><a href="search.php?cat=' . $row['idCat'] . '">' . $row['nomeCat'] . '</a> - ' . $tipo . '</small>
                                </div>
                                <div class="text">
                                    ' . $text . '
                                </div>
                                <div>
                                    <br/>
                                    <small class="tipo"><i class="icon-calendar"></i>' . $time . ' | <a href="profile.php?id=' . $row['idUser'] . '"><i class="icon-user"></i>' . $row['name'] . ' ' . $row['secondname'] . '</a> | <i class="icon-comment"></i> ' . $numProposte . ' Proposte</small>
                                </div>
						    </div>
						</blockquote>
				    </div><br/>';
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


    public function randomUsers()
    {
        $conn = new myConnection();
        $conn->connect();

        $usersPhoto = '';

        /*$numUsers = mysql_fetch_assoc(mysql_query("SELECT COUNT(idUser) as num FROM users"));
        $numUsers = $numUsers['num'];
        if($numUsers > 0)
        {

            //se utente ha amici
            if($numUsers > 6)
            {
                $i = "0";

                $array = array(
                    "0" => "",
                    "1" => "",
                    "2" => "",
                    "3" => "",
                    "4" => "",
                    "5" => "",
                );

                do
                {
                    $random = rand(0,($numUsers-1));
                    $query = mysql_query("SELECT idUser FROM users WHERE idUser='$random'") or die(mysql_error());
                    $row = mysql_fetch_assoc($query);// ottengo le info
                    $idUser  = $row['idUser'];
                    $changed = false;
                    $entered = false;

                    // controllo se ho gia' fatto vedere questo utente o no
                    for($c=0;$c<6;$c++)
                    {
                        if($idUser == $array[$c])
                        {
                            $changed = true;
                        }
                        else
                        {
                            // inserisco nel vettore
                            if($entered == false && $array[$c] == "")
                            {
                                $array[$c] = $idUser;
                                $entered = true;
                            }
                        }
                    }

                    // se non l'ho fatto vedere allora...
                    if($changed == false)
                    {
                        $row = mysql_fetch_assoc(mysql_query("SELECT img, name FROM users WHERE idUser='$idUser'")) or die(mysql_error());
                        //$nameFriend= $row['name'];
                        $nameFriend= mb_substr($row['name'],0,8);
                        $usersPhoto .= '<div><a href="profile.php?id=' . $idUser . '"><img src="' . $row['img'] . '" />' . $nameFriend . '</a></div>';
                        $i++;
                    }
                }while($i < 6);
                unset($array); // cancello il vettore;


            }
            else
            {
                while($row = mysql_fetch_assoc(mysql_query("SELECT idUser, name, img FROM users")))// ottengo le info
                {
                    $idUser  = $row['idUser'];
                    $row = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE idUser='$idUser'")) or die(mysql_error());
                    $nameFriend= $row['name'];
                    $nameFriend= mb_substr($nameFriend,0,8);
                    $usersPhoto .= '<div><a href="profile.php?id=' . $idUser . '"><img src="' . $row['img'] . '" />' . $nameFriend . '</a></div>';
                }
            }
            $usersPhoto .= '</div>';


        }*/


        $q = mysql_num_rows(mysql_query("SELECT idUser FROM users"));
        if($q < 6)
        {
            $n = $q;
        }
        else
        {
            $n = 5;
        }
        for($i=0;$i<$n;$i++)
        {
            if($i == 0)
            {
                $row = mysql_fetch_assoc(mysql_query("SELECT idUser, name, img, feedPos, feedNeg  FROM users ORDER BY feedPos DESC LIMIT 1")) or die(mysql_error());
                $idUser  = 'idUser!=' . $row['idUser'];
            }
            else
            {
                $row = mysql_fetch_assoc(mysql_query("SELECT idUser, name, img, feedPos, feedNeg FROM users WHERE $idUser ORDER BY feedPos DESC LIMIT 1")) or die(mysql_error());
                $idUser  = $idUser . ' AND idUser!=' . $row['idUser'];
            }

            $tipo = $row['tipo'];
            if($tipo == 1)
            {
                $tipo = "Azienda";
            }
            else
            {
                $tipo = "Privato";
            }
            $nameFriend= $row['name'];
            //$nameFriend= mb_substr($nameFriend,0,10);
            $usersPhoto .= '<div class="span2"><div class="row"><div class="span1"><a href="profile.php?id=' . $row['idUser'] . '"><img alt="" src="' . $row['img'] . '" width="64" /></a></div><div class="span1"><a href="profile.php?id=' . $row['idUser'] . '">' . $nameFriend . '</a><br/><i class="icon-thumbs-up icon-white"></i>' . $row['feedPos'] . ' <i class="icon-thumbs-down icon-white"></i>' . $row['feedNeg'] . '<br/>' . $tipo . '</div></div></div>';
        }
        $conn->close();
        return $usersPhoto;

    }

}

class consegna
{
    function uploadFile($idProj,$dirFile,$comment)
    {
        $conn = new myConnection;
        $conn->connect();
        mysql_query("UPDATE project SET delivered='1', file='$dirFile', comment='$comment'  WHERE idProj='$idProj'") or die(mysql_error());
        $conn->close();
    }

    function view($msg, $idProj)
    {
        $msg = $msg;
        $conn = new myConnection;
        $conn->connect();
        $exist = mysql_query("SELECT COUNT(*) as num FROM project WHERE idProj='$idProj' AND delivered='1'") or die(mysql_error());
        $exist = mysql_fetch_assoc($exist);

        if($exist['num'] == 1)
        {
            $exist = true;
        }
        else
        {
            $exist = false;
        }
        include('template/consegna.html');
    }

    function paypal($paypal, $idProj)
    {
        $conn = new myConnection;
        $conn->connect();

        $parser = new parser();
        $paypal = $parser->textParsing($paypal);
        if($paypal != '')
        {
            if(mysql_num_rows(mysql_query("SELECT idProj FROM vincenti WHERE idProj='$idProj' AND paypal='' ")) == 1)
            {
                // se' non ha ancora inserito paypal
                mysql_query("UPDATE vincenti SET paypal='$paypal' WHERE idProj='$idProj'") or die(mysql_error());
            }
        }
        $conn->close();
    }
}


class ready
{
    function readyStart($idProj)
    {
        $idUser = $_SESSION['idUser'];

        $conn = new myConnection;
        $conn->connect();

        // controllo se utente e' proprietario del progetto
        $query = mysql_query("SELECT * FROM project,categorie WHERE project.idProj='$idProj' AND project.idUser='$idUser' AND project.ownerPaid='1' AND project.delivered='1' AND categorie.idCat=project.categoria") or die(mysql_error());
        if(mysql_num_rows($query) == 1)
        {
            $projInfo = mysql_fetch_assoc($query);
            // info sul progetto
            $projName	    = $projInfo['nomeProj'];
            $projPrezzo	    = $projInfo['prezzo'];
            $projCat	    = $projInfo['nomeCat'];
            $commentProj    = $projInfo['comment'];


            // info su utente che ha fatto il progetto
            $vincenteInfo = mysql_query("SELECT * FROM vincenti,users WHERE vincenti.idProj='$idProj' AND users.idUser=vincenti.idUser") or die(mysql_error());
            $vincenteInfo = mysql_fetch_assoc($vincenteInfo);
            $winID			= $vincenteInfo['idUser'];
            $winImg			= $vincenteInfo['img'];
            $winName		= $vincenteInfo['name'];
            $winSecondname	= $vincenteInfo['secondname'];

            $existFeedBack   = $this->existFeedBack($idProj);

            $existFile       = $this->existFile($idProj);

            $file            = $this->returnFile($idProj);

            include('template/ready.html');
        }
        else
        {
            // in caso che ci sia un errore
            $conn->close();
            header('location:index.php');
            exit();
        }
        $conn->close();
    }

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

    function existFile($idProj)
    {
        if(mysql_num_rows(mysql_query("SELECT * FROM project WHERE idProj='$idProj' AND delivered='1'")) == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function returnFile($idProj)
    {
        $file = mysql_query("SELECT file FROM project WHERE idProj='$idProj'");
        $file = mysql_fetch_assoc($file);
        $file = $file['file'];
        return $file;
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

            // feed
            $feedPos		= $row['feedPos'];
            $feedNeg		= $row['feedNeg'];

            if($tipo == 1)
            {
                $tipo = "Azienda";
            }
            else
            {
                $tipo = "Privato";
            }


            $users .= '<table class="table">
                        <tr>
                            <td class="span2">
                                <img src="' .  $img  . '" alt="" width="100" height="100" class="img-polaroid"/>
                            </td>
                            <td>
                                <a href="profile.php?id=' . $userId . '">' . $name . ' ' . $secondname . '</a>
                                <p>' . $tipo . '</p>
                                <p>' . $city . '</p>
                            </td>
                            <td>
                                <p class="text-right">Feed:<i class="icon-thumbs-up"></i><span class="text-success">' . $feedPos . '</span> <i class="icon-thumbs-down"></i><span class="text-error">' . $feedNeg . '</span></p>
                            </td>
                        </tr>
                      </table>';
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
            $msg = new messagges($_SESSION['idUser']);
            $msgNum = $msg->counterUnread($_SESSION['idUser']);

            $menu = '<li><a href="profile.php">Profilo</a></li>
                     <li><a href="mail.php">Messaggi ' . $msgNum . '</a></li>
                     <li><a href="logout.php">Logout</a></li>';
        }
        else
        {

            $menu = '<li><a href="index.php#prog">Progetti</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="registration.php">Registrazione</a></li>';

        }
        return $menu;
    }

    function counterEvents()
    {

    }

}


class parser
{
    function textParsing($textTosend)
    {
        // parsing
        $textTosend = str_replace("\\r\\n", " ", $textTosend);
        $textTosend = str_replace("\\n", " ", $textTosend);
        $textTosend = str_replace("\\r", " ", $textTosend);
        $textTosend = preg_replace('/\'/i','&#39;', $textTosend);
        $textTosend = preg_replace('/`/i','&#96;', $textTosend);
        $textTosend = stripslashes($textTosend);
        $textTosend = strip_tags($textTosend);
        $textTosend = mysql_real_escape_string($textTosend);

        $textTosend = trim($textTosend, '\r\n');
        //$textTosend = preg_replace('/\'/i','&#39;', $textTosend);
        //$textTosend = preg_replace('/`/i','&#96;', $textTosend);
        $textTosend = mysql_real_escape_string($textTosend);

        return $textTosend;
    }

    function textParsingWithNL($textTosend)
    {
        // parsing lasciando <br />

        $textTosend = str_replace("\\r\\n", "<br />", $textTosend);
        $textTosend = str_replace("\\n", "<br />", $textTosend);
        $textTosend = str_replace("\\r", "<br />", $textTosend);
        $textTosend = stripslashes($textTosend);
        $textTosend = strip_tags($textTosend, '<br>');
        $textTosend = mysql_real_escape_string($textTosend);

        $textTosend = trim($textTosend, '\r\n');
        $textTosend = preg_replace('/\'/i','&#39;', $textTosend);
        $textTosend = preg_replace('/`/i','&#96;', $textTosend);
        $textTosend = mysql_real_escape_string($textTosend);

        return $textTosend;
    }
}

class comments
{
    function comment($row)
    {
        $time = strftime("%H:%M, %d %b %Y", strtotime($row['data']));
        $parser = new parser();
        $text = $row['comment'];
        // parsing di commento
        $text = $parser->textParsingWithNL($text);
        $comments ='';
        $comments .= '<div class="media ">
						  <a class="pull-left" href="profile.php?id=' . $row['idUser'] .'">
							<img class="media-object" src="' . $row['img'] . '" width="64px">
						  </a>
						  <div class="media-body">
                            <h4 class="media-heading"><a href="profile.php?id=' . $row['idUser'] . '">' . $row['name'] . ' ' . $row['secondname'] . '</a></h4>
                            ' . $text . '
							<div class="media pull-right">
							  <small><i class="icon-calendar"></i> ' . $time . '</small>
							</div>
                          </div>
		              </div>';

        return $comments;
    }


}
?>