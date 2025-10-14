<?php // Conexión con la base de datos en PDO.
session_start(); // Incluyo el session_start() ya que se usará en casi todos los scripts.
try // Intenta la conexión
{
	// Obtener la contraseña desde la variable de entorno
	$password = getenv('MySQL');
	
	// Si no se encuentra la variable de entorno, lanzar error
	if ($password === false) {
		throw new Exception('Variable de entorno MySQL no encontrada');
	}
	
	$conn = new PDO('mysql:host=localhost;dbname=barbery', "root", $password);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e) // En caso de error de PDO
{
	echo 'Error de conexión: ' . $e->getMessage(); // Muestra el error.
}
catch(Exception $e) // En caso de error de variable de entorno
{
	echo 'Error de configuración: ' . $e->getMessage(); // Muestra el error.
}
?>