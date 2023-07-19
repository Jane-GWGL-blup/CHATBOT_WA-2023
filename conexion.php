<?php
//conexion.php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$servername =$_ENV['DATABASE_HOST'];
$username = $_ENV['DATABASE_USERNAME'];
$password = $_ENV['DATABASE_PASSWORD'];
$dbname = $_ENV['DATABASE_NAME'];
// Creando la conexión
$conn = new mysqli($servername, $username, $password, $dbname);