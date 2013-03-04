<?php
	
	include('model.php');
	$connect = new myConnection;
	$connect->connect();
	$model = new ModelSearchCategory;
	if(isset($_GET['cat']))
	{
		$categoria 	= $_GET['cat'];
		
		if(isset($_GET['page']))
		{
			$page = $_GET['page'];
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
			$model->searchByWord($search);
		}
		else
		{
			header('location:index.php');	
		}
	}
	
	$connect->close();
?>