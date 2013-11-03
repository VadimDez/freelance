<?php
    //include('checklogin.php');
    include('dbConnect.php');

    $friends = '';
    
    
    
    if(isset($_GET['search_name']))
    {
    
        $listUsers = "1";
        
        if(isset($_GET['id'])) // se passa id - quindi e' la lista degli agmici, altrimenti e' la lista degli utenti
        {
            $listUsers = "0";
            $id    = $_GET['id'];
        }
        include_once('model.php');
        $parser = new parser();
        $name  = $parser->textParsing($_GET['search_name']);
        
        
        $array = explode(' ', $name); // per dividere la stringa in NOME e COGNOME
        
        $nome = "";
        $cognome ="";
        
        foreach($array as $key => $value){
            if("{$key}" == "0")
            {
                $nome = "{$value}";
            }
            else if("{$key}" == "1")
            {
                $cognome = "{$value}";
            }
        }
        if($name != "")
        {
            $name  = mysql_real_escape_string($name);
            if($listUsers == "0")
            {   // la lista degli amici
            
                if(empty($cognome)) // se la stringa 'cognome' vuota allora non la uso
                {
                    $query = mysql_query("SELECT * FROM users, friends WHERE member.id = friends.idFriend AND friends.idUser='$id' AND (member.name LIKE '%$name%' OR member.secondname LIKE '%$name%') ORDER BY member.secondname") or die (mysql_error());
                }
                else
                {
                    $query = mysql_query("SELECT * FROM member, friends WHERE member.id = friends.idFriend AND friends.idUser='$id' AND ((member.name LIKE '%$name%' OR member.secondname LIKE '%$name%') OR (member.name LIKE '%$nome%' AND member.secondname LIKE '%$cognome%')) ORDER BY member.secondname") or die (mysql_error());
                }
            }
            else
            {   // la lista dei utenti
                
                if(empty($cognome)) // se la stringa 'cognome' vuota allora non la uso
                {
                    
                    $query = mysql_query("SELECT * FROM users WHERE name LIKE '%$nome%' OR secondname LIKE '%$nome%' ORDER BY secondname") or die (mysql_error());
                    
                }
                else
                {
                    $query = mysql_query("SELECT * FROM users WHERE (name LIKE '%$name%' OR secondname LIKE '%$name%') OR (name LIKE '%$nome%' AND secondname LIKE '%$cognome%') ORDER BY secondname") or die (mysql_error());
                }
            }
            if(mysql_num_rows($query) > "0")
            {
                $friends = '';
                while($row = mysql_fetch_assoc($query))
                {

                    $userId     = $row['idUser'];
                    $name       = $row['name'];
                    $secondname = $row['secondname'];
                    $img        = $row['img'];
                    $city       = $row['city'];
                    $tipo       = $row['tipo'];

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
                    
                    $friends .= '<table class="table">
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
                $friends .= '';
                print "$friends";
            }
            else
            {
                if($listUsers == "0")
                {
                    $friends = "0 amcici con nome \"$name\"";
                }
                else
                {
                    $friends = "0 utenti con nome \"$name\"";
                }
                print "$friends";
            }
        }
        
        
    }
    

?>