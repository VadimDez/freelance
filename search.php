<?php
	
	include('model.php');
    $parser = new parser();
	$connect = new myConnection;
	$connect->connect();
	$model = new ModelSearchCategory;
	if(isset($_GET['cat']))
	{
		$categoria 	= $parser->textParsing($_GET['cat']);
		
		if(isset($_GET['page']))
		{
			$page = $parser->textParsing($_GET['page']);
			$model->searchCategory($categoria,$page);
		}
		else
		{
			$model->searchCategory($categoria);
		}
	}
	else
	{
		if(isset($_GET['search']) && $_GET['search'] != '')
		{
			$search = $_GET['search'];
            $search = $parser->textParsing($search);
			$model->searchByWord(1,$search);
            $_SESSION['search'] = $search;
		}
		else
		{
            if($_GET['page'])
            {
                $page = $parser->textParsing($_GET['page']);
                $search = $_SESSION['search'];
                $model->searchByWord($page,$search);
            }
            else
            {

                header('location:index.php');
                $connect->close();
                exit();
            }
		}
	}
	
	$connect->close();
?>