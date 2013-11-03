<?php
/**
 * Created by JetBrains PhpStorm.
 * User: vadimdez
 * Date: 3/27/13
 * Time: 8:52 PM
 * algoritmo per cambiare il nome al file
 */

if(include_once('check.php'))
{
    if(isset($_POST['id']) && $_POST['id'] != '')
    {
        $idProj = $_POST['id'];
        include_once('model.php');
        $conn = new myConnection();
        if($conn->connect())
        {
            $sid = session_id();
            if(mysql_num_rows(mysql_query("SELECT p.idUser FROM session as s, project as p WHERE p.idUser=s.idUser AND s.session='$sid' AND p.idProj='$idProj'")) == 1)
            {
                // assume a link like this: http://www.site.com/download/?id=filename
                $filename = mysql_fetch_assoc(mysql_query("SELECT file FROM project WHERE idProj='$idProj'")) or die(mysql_error());
                $filename = $filename['file'];
                // generated filename, uses current timestamp for example
                $new_filename = time();
                if (file_exists($filename))
                {
                    header('Content-type:application/rar');
                    header('Content-Disposition: attachment; filename ="'.$new_filename.'.rar"');
                    readfile($filename);
                }
                else
                {
                    $conn->close();
                    header('location:index.php');
                    exit();
                }
            }
            else
            {
                $conn->close();
                header('location:index.php');
                exit();
            }
        }
        $conn->close();
    }
    else
    {
        header('location:index.php');
        exit();
    }
}


?>