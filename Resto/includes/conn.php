<?php
session_start();
ob_start();
try
{
	$mysqlPassword = getenv('MySQL');
	if (!$mysqlPassword) {
		throw new Exception('La variable de entorno MySQL no está definida');
	}
	
	$conn = new PDO('mysql:host=localhost;dbname=resto', "root", $mysqlPassword);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
	echo 'Error: ' . $e->getMessage();
}
catch(Exception $e)
{
	echo 'Error: ' . $e->getMessage();
}
?>